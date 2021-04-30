<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/05
 * Time: 02:03
 */

namespace App\Application\Handlers\Product\Variation;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class UpdateVariation extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * UpdateVariation constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['variationId'])) {
            throw new \InvalidArgumentException('variation id not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $variationId = $data['variationId'];
        unset($data['variationId']);

        $response = $this->requestSender->services->products->updateVariation($variationId, $data);
        $data['variation'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}