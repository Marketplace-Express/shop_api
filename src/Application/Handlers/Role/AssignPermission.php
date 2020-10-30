<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/24
 * Time: 13:53
 */

namespace App\Application\Handlers\Role;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class AssignPermission extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * AssignPermission constructor.
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

        $this->requestSender->services->users->assignPermission($data['roleId'], $data['permission']);

        return parent::handle($data);
    }
}