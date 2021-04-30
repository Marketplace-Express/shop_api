<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/27
 * Time: 13:14
 */

namespace App\Application\Handlers\Product\Product;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class CreateProduct extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * CreateProduct constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['user'])) {
            throw new \InvalidArgumentException('user data is not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        if (empty($data['categories'])) {
            throw new \InvalidArgumentException('category not found or maybe deleted', StatusCodeInterface::STATUS_NOT_FOUND);
        }

        $data['product']['userId'] = $data['user']['user_id'];

        $response = $this->requestSender->services->products->create($data['product']);
        $data['product'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}