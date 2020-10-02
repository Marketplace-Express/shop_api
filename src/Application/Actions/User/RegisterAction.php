<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/13
 * Time: 15:53
 */

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Application\Chains\RegisterChain;
use App\Utilities\RequestSender;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class RegisterAction extends Action
{
    /**
     * @var RequestSender
     */
    private $requestSender;

    private $chainRequest;

    public function __construct(LoggerInterface $logger, RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
        $this->logger = $logger;
        $this->chainRequest = (new RegisterChain($requestSender, $logger))->initiate();
        parent::__construct($logger);
    }

    protected function action(): Response
    {
        try {
            $response = $this->chainRequest->run(
                json_decode($this->request->getBody()->getContents(), true)
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}