<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/10
 * Time: 14:36
 */

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Application\Chains\User\UnBanChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class UnBanAction extends Action
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    private $chain;

    public function __construct(LoggerInterface $logger, RequestSenderInterface $requestSender)
    {
        $this->logger = $logger;
        $this->requestSender = $requestSender;
        $this->chain = (new UnBanChain($requestSender, $logger))->initiate();
    }

    protected function action(): Response
    {
        try {
            $this->chain->run(
                ['userId' => $this->request->getAttribute('userId')]
            );

            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => ''];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}