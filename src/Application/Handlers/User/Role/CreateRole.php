<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/23
 * Time: 13:25
 */

namespace App\Application\Handlers\User\Role;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

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
        if (empty($data['role_name']) || empty($data['storeId'])) {
            throw new \InvalidArgumentException('role name or store id not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $response = $this->requestSender->services->users->createRole($data['role_name'], $data['storeId']);
        $data['role'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}