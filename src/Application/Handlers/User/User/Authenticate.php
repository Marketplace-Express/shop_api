<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 11:46
 */

namespace App\Application\Handlers\User\User;


use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;

class Authenticate extends AbstractUserAccess
{
    /**
     * @var \Closure|null
     */
    private $callable;

    /**
     * Authenticate constructor.
     * @param RequestSenderInterface $requestSender
     * @param ServerRequestInterface $request
     * @param TokenAuthentication $tokenAuthentication
     * @param \Closure|null $callable
     */
    public function __construct(
        RequestSenderInterface $requestSender,
        ServerRequestInterface $request,
        TokenAuthentication $tokenAuthentication,
        \Closure $callable = null
    ) {
        parent::__construct($requestSender, $request, $tokenAuthentication);
        $this->callable = $callable;
    }

    public function handle(array $data = [])
    {
        $response = $this->requestSender->services->users->isAuthenticated(
            $this->getToken(),
            ['csrf-token' => $this->getCsrfToken()]
        );

        if (empty($response['message']['is_authenticated'])) {
            throw new \Exception('unauthorized', StatusCodeInterface::STATUS_UNAUTHORIZED);
        }

        $data['user'] = $response['message']['user'];

        if ($this->callable) {
            $data = call_user_func_array($this->callable, [$data]);
        }

        return parent::handle($data);
    }
}