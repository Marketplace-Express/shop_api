<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 13:36
 */

namespace App\Application\Handlers\Store;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class FollowStore extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * FollowStore constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['storeId']) || empty($data['user'])) {
            throw new \InvalidArgumentException('store id or user data not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $this->requestSender->services->stores->follow($data['storeId'], $data['user']['user_id']);

        return parent::handle($data);
    }
}