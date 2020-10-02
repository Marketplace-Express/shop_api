<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/18
 * Time: 16:08
 */

namespace App\Utilities;


interface RequestSenderInterface
{
    /**
     * @param string $queueName
     * @return RequestSenderInterface
     */
    public function setQueueName(string $queueName);

    /**
     * @param string $service
     * @return RequestSenderInterface
     */
    public function setRoute(string $service);

    /**
     * @param string $method
     * @return RequestSenderInterface
     */
    public function setMethod(string $method);

    /**
     * @param array $data
     * @return RequestSenderInterface
     */
    public function setBody(array $data);

    /**
     * Send sync request to another endpoint
     * and waiting response
     *
     * @return mixed
     */
    public function sendSync();

    public function sendAsync();
}