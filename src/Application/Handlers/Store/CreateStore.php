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
        if (empty($data['user'])) {
            throw new \InvalidArgumentException('user data not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $data['ownerId'] = $data['user']['user_id'];

        // do not send extra data to stores service
        unset($data['user']);

        $response = $this->requestSender->services->stores->create($data);

        $data['store'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}