<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/31
 * Time: 00:11
 */

namespace App\Application\Handlers\Store;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class GetFollowedStores extends AbstractHandler
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_LIMIT = 10;

    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * GetFollowedStores constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['user'])) {
            throw new \InvalidArgumentException('user data not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $data['page'] = $data['page'] ?? self::DEFAULT_PAGE;
        $data['limit'] = $data['limit'] ?? self::DEFAULT_LIMIT;

        $response = $this->requestSender->services->stores->getFollowed($data['user']['user_id'], $data['page'], $data['limit']);
        $data['followed_stores'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        } else {
            return $response;
        }
    }
}