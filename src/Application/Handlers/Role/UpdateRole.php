<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 01:12
 */

namespace App\Application\Handlers\Role;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class UpdateRole extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * UpdateRole constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['role_id']) || empty($data['role_name'])) {
            throw new \InvalidArgumentException('role id or role name not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $response = $this->requestSender->services->users->updateRole($data['role_id'], $data['role_name']);
        $data['role'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}