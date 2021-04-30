<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/14
 * Time: 12:44
 */

namespace App\Application\Actions\Product\Product;


use App\Application\Actions\Action;
use App\Application\Chains\Product\Product\GetProductChain;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class GetProductAction extends Action
{
    private $chain;

    /**
     * GetProductAction constructor.
     * @param RequestSenderInterface $requestSender
     * @param LoggerInterface $logger
     */
    public function __construct(RequestSenderInterface $requestSender, LoggerInterface $logger)
    {
        $this->chain = (new GetProductChain($requestSender, $logger))->initiate();
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                ['productId' => $this->request->getAttribute('productId')]
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}