<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 14:34
 */

namespace App\Application\Handlers\Store;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class CreateStore extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * CreateStore constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['user']) || empty($data['user']['user_id'])) {
            throw new \InvalidArgumentException('user id not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $response = $this->requestSender->services->stores->create(
            $data['user']['user_id'],
            $data['name'],
            $data['description'],
            $data['type'],
            $data['location'],
            $data['photo'],
            $data['coverPhoto']
        );

        return parent::handle($response);
    }
}