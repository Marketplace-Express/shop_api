<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/22
 * Time: 00:17
 */

namespace App\Application\Actions\Product;


use App\Application\Actions\Action;
use App\Application\Chains\Product\GetProductsChain;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ResponseInterface as Response;

class GetProductsAction extends Action
{
    /**
     * @var GetProductsChain
     */
    private $chain;

    /**
     * GetProductsAction constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->chain = (new GetProductsChain($requestSender))->initiate();
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                $this->request->getQueryParams()
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}