<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/24
 * Time: 01:41
 */

namespace App\Application\Handlers\Role;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class DeleteRole extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * DeleteRole constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['roleId'])) {
            throw new \InvalidArgumentException('role id not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $this->requestSender->services->users->deleteRole($data['roleId']);

        return parent::handle($data);
    }
}