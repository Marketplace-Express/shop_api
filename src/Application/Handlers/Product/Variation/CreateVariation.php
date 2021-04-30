<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/04
 * Time: 21:46
 */

namespace App\Application\Handlers\Product\Variation;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;

class CreateVariation extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * CreateVariation constructor.
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

        $data['userId'] = $data['user']['user_id'];

        $response = $this->requestSender->services->products->createVariation($data);
        $data['variation'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }
}