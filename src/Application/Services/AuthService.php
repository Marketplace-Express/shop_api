<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/08/26
 * Time: 22:38
 */

namespace App\Application\Services;



use App\Application\Actions\Permissions;
use App\Utilities\RequestSenderInterface;

/**
 * Class AuthServiceProvider
 * @package App\Providers\Auth
 */
class AuthService
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * AuthService constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    /**
     * @param string $token
     * @param string $csrfToken
     * @return bool
     */
    public function isAuthenticated(string $token, string $csrfToken): bool
    {
        $headers = [
            'csrf-token' => $csrfToken
        ];

        $response = $this->requestSender->services->users->isAuthenticated($token, $headers);

        return $response['message'];
    }

    /**
     * @param string $token
     * @param string $csrfToken
     * @param array $permissions
     * @param string $operator
     * @return bool
     */
    public function isAuthorized(string $token, string $csrfToken, array $permissions, string $operator): bool
    {
        if (empty($operator)) {
            $operator = Permissions::OPERATOR_AND;
        }

        $headers = [
            'csrf-token' => $csrfToken
        ];

        $response = $this->requestSender->services->users->isAuthorized($token, $headers, $permissions, $operator);

        return $response['message'];
    }
}