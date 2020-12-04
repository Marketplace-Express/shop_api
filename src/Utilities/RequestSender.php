<?php
/**
 * User: Wajdi Jurry
 * Date: 16/02/19
 * Time: 06:27 م
 */

namespace App\Utilities;


use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class RequestSender implements RequestSenderInterface
{
    const REQUEST_TYPE_SYNC = 'sync';
    const REQUEST_TYPE_ASYNC = 'async';

    private $queueName;
    private $route;
    private $method;
    private $correlationId;
    private $headers = [];
    private $body = [];
    private $query = [];
    private $replyTo = null;
    private $exchange = null;
    private $requestType;

    /** @var AMQPChannel */
    private $channel;

    /** @var AMQPMessage */
    private $response;

    /**
     * @var Services
     */
    public $services;

    /**
     * QueueRequestHandler constructor.
     * @param AmqpHandler $handler
     * @param array $services
     */
    public function __construct(AmqpHandler $handler, array $services = [])
    {
        $this->channel = $handler->getChannel();
        $this->services = new Services($this, $services);
    }

    /**
     * @return string
     */
    public function getCorrelationId(): string
    {
        return $this->correlationId = uniqid('', true);
    }

    /**
     * @param string $queueName
     * @return RequestSender
     */
    public function setQueueName(string $queueName)
    {
        $this->queueName = $queueName;

        return $this;
    }

    /**
     * @param string $route
     * @return RequestSender
     */
    public function setRoute(string $route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * @param string $method
     * @return RequestSender
     */
    public function setMethod(string $method)
    {
        $this->method = $method;

        return $this;
    }

    public function setHeaders(array $headers = [])
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @param array $body
     * @return RequestSender
     */
    public function setBody(array $body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @param array $query
     * @return $this
     */
    public function setQuery(array $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @param string $replyTo
     * @return RequestSender
     */
    public function setReplyTo(string $replyTo)
    {
        $this->replyTo = $replyTo;

        return $this;
    }

    /**
     * @param string $exchange
     * @return RequestSender
     */
    public function setExchange(string $exchange)
    {
        $this->exchange = $exchange;

        return $this;
    }

    /**
     * Initialize consumer for Sync requests
     * @throws \Exception
     */
    private function initializeConsumer(): void
    {
        if (empty($this->replyTo)) {
            throw new \Exception('Property "reply_to" is missing');
        }
        $this->channel->basic_consume($this->replyTo, '', false, false, false, false, [
            $this,
            'getResponse'
        ]);
    }

    /**
     * Wait response for Sync requests
     * @throws \ErrorException
     */
    private function waitResponse(): void
    {
        while (!isset($this->response)) {
            $this->channel->wait(null, false, 10);
        }
    }

    /**
     * @param AMQPMessage $response
     * @throws \Exception
     */
    public function getResponse(AMQPMessage $response)
    {
        if ($response->get('correlation_id') == $this->correlationId) {
            $this->response = json_decode($response->getBody(), true) ?? [];
            if (array_key_exists('hasError', $this->response) && true === $this->response['hasError']) {
                $this->channel->basic_ack($response->delivery_info['delivery_tag']);
                throw new \Exception($this->response['message'], $this->response['status']);
            }
        }
    }

    /**
     * Send sync request to another endpoint
     * and waiting response
     *
     * @return mixed
     *
     * @throws \ErrorException
     * @throws \Exception
     */
    public function sendSync()
    {
        $this->requestType = self::REQUEST_TYPE_SYNC;

        list($this->replyTo, ,) = $this->channel->queue_declare('', false, true, true, true);

        $this->initializeConsumer();
        $message = new AMQPMessage(json_encode([
            'route' => $this->route,
            'method' => $this->method,
            'headers' => $this->headers,
            'query' => $this->query,
            'body' => $this->body,
        ]), [
            'reply_to' => $this->replyTo,
            'correlation_id' => $this->getCorrelationId(),
            'deliver_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT
        ]);
        $this->channel->basic_publish($message, $this->exchange, $this->queueName);

        // Waiting response
        $this->waitResponse();

        // Store response in variable and unset the original one
        $response = $this->response;
        unset($this->response);

        // Return response
        return $response;
    }

    public function sendAsync()
    {
        $this->requestType = self::REQUEST_TYPE_ASYNC;

        $message = new AMQPMessage(json_encode([
            'route' => $this->route,
            'method' => $this->method,
            'headers' => $this->headers,
            'body' => $this->body,
            'query' => $this->query
        ]));
        $this->channel->basic_publish($message, $this->exchange, $this->queueName);
    }
}