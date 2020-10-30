<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 10:58
 */

namespace App\Application\Handlers\Store;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class IsStoreOwner extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var string
     */
    private $storeId;

    /**
     * StoreOwner constructor.
     * @param RequestSenderInterface $requestSender
     * @param $storeId
     */
    public function __construct(RequestSenderInterface $requestSender, $storeId)
    {
        $this->requestSender = $requestSender;
        $this->storeId = $storeId;
    }

    public function handle(array $data = [])
    {
        if (empty($data['user']) || empty($this->storeId)) {
            throw new \InvalidArgumentException('user id or store id not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $response = $this->requestSender->services->stores->isStoreOwner($data['user']['user_id'], $this->storeId);
        $data = array_merge($data, ['store_owner' => $response['message']]);

        return parent::handle($data);
    }
}