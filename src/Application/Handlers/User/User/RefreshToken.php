<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/13
 * Time: 15:41
 */

namespace App\Application\Handlers\User\User;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class RefreshToken extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * RefreshToken constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $response = $this->requestSender->services->users->refreshToken($data['refresh_token']);

        $data['token'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}