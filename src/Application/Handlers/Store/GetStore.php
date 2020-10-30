<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/23
 * Time: 14:52
 */

namespace App\Application\Handlers\Store;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class GetStore extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['storeId'])) {
            throw new \Exception('store id not provided', 400);
        }

        $response = $this->requestSender->services->stores->getById($data['storeId']);
        $data['store'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        } else {
            return $response['message'];
        }
    }
}