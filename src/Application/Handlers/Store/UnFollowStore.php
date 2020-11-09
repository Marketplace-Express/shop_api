<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/31
 * Time: 09:59
 */

namespace App\Application\Handlers\Store;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class UnFollowStore extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * UnFollow constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $data['storeId'] = $data['storeId'] ?? null;

        $this->requestSender->services->stores->unfollow($data['user']['user_id'], $data['storeId']);

        return parent::handle($data);
    }
}