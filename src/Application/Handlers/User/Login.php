<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/03
 * Time: 15:32
 */

namespace App\Application\Handlers\User;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class Login extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    /**
     * @param array $data
     * @return array|mixed
     */
    public function handle(array $data = [])
    {
        $response = $this->requestSender->services->users->login($data);

        return parent::handle($response);
    }
}