<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/31
 * Time: 01:21
 */

namespace App\Application\Handlers\User\User;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class GetUsersByIds extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * GetByIds constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['users_ids'])) {
            throw new \InvalidArgumentException('users ids not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $response = $this->requestSender->services->users->getByIds($data['users_ids']);
        $data['users'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}