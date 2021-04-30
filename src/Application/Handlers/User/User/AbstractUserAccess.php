<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 11:49
 */

namespace App\Application\Handlers\User\User;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;
use Slim\Middleware\TokenAuthentication\TokenNotFoundException;

abstract class AbstractUserAccess extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    protected $requestSender;

    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * @var TokenAuthentication
     */
    protected $tokenAuthentication;

    /**
     * AbstractUserAccess constructor.
     * @param RequestSenderInterface $requestSender
     * @param ServerRequestInterface $request
     * @param TokenAuthentication $tokenAuthentication
     */
    public function __construct(
        RequestSenderInterface $requestSender,
        ServerRequestInterface $request,
        TokenAuthentication $tokenAuthentication
    ) {
        $this->requestSender = $requestSender;
        $this->request = $request;
        $this->tokenAuthentication = $tokenAuthentication;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function getToken()
    {
        try {
            return $this->tokenAuthentication->findToken($this->request);
        } catch (TokenNotFoundException $exception) {
            throw new \Exception('unauthorized', StatusCodeInterface::STATUS_UNAUTHORIZED);
        }
    }

    /**
     * @return string
     */
    protected function getCsrfToken()
    {
        return $this->request->getHeaderLine('csrf-token');
    }
}