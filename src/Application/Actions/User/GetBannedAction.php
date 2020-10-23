<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/16
 * Time: 12:24
 */

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Application\Chains\User\GetBannedChain;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetBannedAction extends Action
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    private $chain;

    public function __construct(RequestSenderInterface $requestSender, LoggerInterface $logger)
    {
        $this->requestSender = $requestSender;
        $this->logger = $logger;
        $this->chain = (new GetBannedChain($requestSender, $logger))->initiate();
    }

    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                $this->request->getQueryParams()
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}