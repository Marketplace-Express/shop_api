<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 17:38
 */

namespace App\Application\Handlers\Role;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class UnAssignRole extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * UnAssignRole constructor.
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

        $this->requestSender->services->users->unAssignRole($data['roleId'], $data['user_id']);

        return parent::handle($data);
    }
}