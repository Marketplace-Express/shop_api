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

        $data['page'] = $data['page'] ?? null;
        $data['limit'] = $data['limit'] ?? null;

        $response = $this->requestSender->services->stores->getFollowers($data['storeId'], $data['page'], $data['limit']);
        $data['followers'] = $data['users_ids'] = $response['message']['followers'];
        $data['followers_count'] = $response['message']['count'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}