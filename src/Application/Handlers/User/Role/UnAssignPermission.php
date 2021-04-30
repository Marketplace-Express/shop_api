<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 00:50
 */

namespace App\Application\Handlers\User\Role;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class UnAssignPermission extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * UnAssignPermission constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['roleId']) || empty($data['permission'])) {
            throw new \InvalidArgumentException('role id or permission not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $this->requestSender->services->users->unAssignPermission($data['roleId'], $data['permission']);

        return parent::handle($data);
    }
}