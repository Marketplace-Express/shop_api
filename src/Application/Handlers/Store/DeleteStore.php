<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/30
 * Time: 00:27
 */

namespace App\Application\Handlers\Store;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class DeleteStore extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * DeleteStore constructor.
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

        $this->requestSender->services->stores->delete($data['storeId']);

        return parent::handle($data);
    }
}