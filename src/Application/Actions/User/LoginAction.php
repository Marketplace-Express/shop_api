<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/03
 * Time: 15:29
 */

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Application\Chains\User\LoginChain;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class LoginAction extends Action
{
    /**
     * @var bool
     */
    protected $forceLogUsage = true;

    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var LoginChain
     */
    private $chain;

    /**
     * LoginAction constructor.
     * @param LoggerInterface $logger
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(LoggerInterface $logger, RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
        $this->chain = (new LoginChain($requestSender, $logger))->initiate();
    }

    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                array_merge(
                    $this->getRequestBody(true),
                    ['csrf-token' => $this->request->getHeaderLine('csrf-token')]
                )
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}