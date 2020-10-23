<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/17
 * Time: 15:28
 */

namespace App\Application\Handlers\User;


use App\Application\Actions\Permissions;
use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;
use Slim\Middleware\TokenAuthentication\TokenNotFoundException;

class Authorize extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var ServerRequestInterface
     */
    private $request;
    /**
     * @var TokenAuthentication
     */
    private $tokenAuthentication;

    /**
     * @var array
     */
    private $authorizeData;

    /**
     * Authorize constructor.
     * @param RequestSenderInterface $requestSender
     * @param ServerRequestInterface $request
     * @param TokenAuthentication $tokenAuthentication
     * @param array $authorizeData
     */
    public function __construct(
        RequestSenderInterface $requestSender,
        ServerRequestInterface $request,
        TokenAuthentication $tokenAuthentication,
        array $authorizeData = []
    ) {
        $this->requestSender = $requestSender;
        $this->request = $request;
        $this->tokenAuthentication = $tokenAuthentication;
        $this->authorizeData = $authorizeData;
    }

    public function handle(array $data = [])
    {
        $permissions = $this->getActionPermissions($this->request->getAttribute('requested_action_as_string'));

        if (empty($permissions->policyModel)) {
            throw new \InvalidArgumentException('policyModel is not provided', 400);
        }

        $isAuthorized = $this->requestSender->services->users->isAuthorized(
            $this->getToken(),
            ['csrf-token' => $this->getCsrfToken()],
            $permissions->grants,
            $permissions->operator,
            $permissions->policyModel,
            $this->authorizeData
        );

        if (empty($isAuthorized['message'])) {
            throw new \Exception('unauthorized', 401);
        }

        return parent::handle($data);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getToken()
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
    private function getCsrfToken()
    {
        return $this->request->getHeaderLine('csrf-token');
    }

    /**
     * @param string $calledAction
     * @return Permissions|object|null
     */
    public function getActionPermissions(string $calledAction)
    {
        try {
            AnnotationRegistry::loadAnnotationClass($calledAction);
            $reflectionClass = new \ReflectionClass($calledAction);
            $method = $reflectionClass->getMethod('action');
            $reader = new AnnotationReader();
            return $reader->getMethodAnnotation(
                    $method,
                    Permissions::class
                ) ?? new Permissions();
        } catch (\Throwable $exception) {
            return new Permissions();
        }
    }
}