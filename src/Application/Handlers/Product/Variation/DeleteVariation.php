<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/05
 * Time: 17:54
 */

namespace App\Application\Handlers\Product\Variation;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class DeleteVariation extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * DeleteVariation constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        if (empty($data['variationId'])) {
            throw new \InvalidArgumentException('variation id is not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $this->requestSender->services->products->deleteVariation($data['variationId']);

        return parent::handle($data);
    }
}