<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/23
 * Time: 13:25
 */

namespace App\Application\Handlers\Role;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class CreateRole extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * CreateRole constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $response = $this->requestSender->services->users->createRole($data['role_name'], $data['store_id']);

        return parent::handle($response);
    }
}