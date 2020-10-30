<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/17
 * Time: 15:28
 */

namespace App\Application\Handlers\User;


use App\Application\Actions\Permissions;
use App\Utilities\RequestSenderInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Middleware\TokenAuthentication;

class Authorize extends AbstractUserAccess
{
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
        parent::__construct($requestSender, $request, $tokenAuthentication);
        $this->authorizeData = $authorizeData;
    }

    public function handle(array $data = [])
    {
        $permissions = $this->getActionPermissions($this->request->getAttribute('requested_action_as_string'));

        if (empty($permissions->policyModel)) {
            throw new \InvalidArgumentException('policy model is not provided', StatusCodeInterface::STATUS_UNAUTHORIZED);
        }

        if (empty($data['user'])) {
            throw new \Exception('unauthorized', StatusCodeInterface::STATUS_UNAUTHORIZED);
        }

        if (isset($data['store_owner'])) {
            $this->authorizeData['storeOwner'] = $data['store_owner'];
        }

        $isAuthorized = $this->requestSender->services->users->isAuthorized(
            $data['user'],
            ['csrf-token' => $this->getCsrfToken()],
            $permissions->grants,
            $permissions->operator,
            $permissions->policyModel,
            $this->authorizeData
        );

        if (empty($isAuthorized['message'])) {
            throw new \Exception('unauthorized', StatusCodeInterface::STATUS_UNAUTHORIZED);
        }

        return parent::handle($data);
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