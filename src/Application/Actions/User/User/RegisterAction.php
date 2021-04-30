<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/13
 * Time: 15:53
 */

namespace App\Application\Actions\User\User;


use App\Application\Actions\Action;
use App\Application\Chains\User\User\RegisterChain;
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

    /**
     * RegisterAction constructor.
     * @param LoggerInterface $logger
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(LoggerInterface $logger, RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
        $this->chainRequest = (new RegisterChain($requestSender, $logger))->initiate();
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        try {
            $response = $this->chainRequest->run(
                $this->getRequestBody(true)
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}