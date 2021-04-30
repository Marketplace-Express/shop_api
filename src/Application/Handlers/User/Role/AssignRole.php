<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 17:25
 */

namespace App\Application\Handlers\User\Role;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class AssignRole extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * AssignRole constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['roleId']) || empty($data['user_id'])) {
            throw new \InvalidArgumentException('role id or user id not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        return $this->requestSender->services->users->assignRole($data['roleId'], $data['user_id']);
    }
}