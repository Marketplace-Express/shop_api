<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/17
 * Time: 10:43
 */

namespace App\Application\Handlers\User\Role;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class GetRole extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $response = $this->requestSender->services->users->getRole($data['roleId']);
        $data['role'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}