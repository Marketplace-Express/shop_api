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
        if (empty($data['store_id'])) {
            throw new \Exception('store_id not provided', 400);
        }

        $this->requestSender->services->stores->getById($data['store_id']);

        return parent::handle($data);
    }
}