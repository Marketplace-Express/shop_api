<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 16:05
 */

namespace App\Application\Handlers\Store;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class GetFollowers extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * GetFollowers constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['storeId'])) {
            throw new \InvalidArgumentException('store id not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        if (empty($data['pagination']['page']) || empty($data['pagination']['limit'])) {
            throw new \InvalidArgumentException('page or limit not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $response = $this->requestSender->services->stores->getFollowers($data['storeId'], $data['pagination']['page'], $data['pagination']['limit']);
        $data = array_merge($data, ['followers' => $response['message']]);

        if ($this->next) {
            return parent::handle($data);
        } else {
            return $response;
        }
    }
}