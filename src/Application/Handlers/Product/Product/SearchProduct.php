<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/12
 * Time: 15:21
 */

namespace App\Application\Handlers\Product\Product;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class SearchProduct extends AbstractHandler
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
        if (empty($data['keyword'])) {
            return ['message' => ['results' => []], 'status' => 200];
        }

        $response = $this->requestSender->services->products->searchProduct($data['keyword']);

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}