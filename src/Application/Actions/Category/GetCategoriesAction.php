<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/06
 * Time: 14:00
 */

namespace App\Application\Actions\Category;


use App\Application\Actions\Action;
use App\Application\Chains\Category\GetCategoriesChain;
use App\Utilities\RequestSenderInterface;
use Psr\Http\Message\ResponseInterface as Response;

class GetCategoriesAction extends Action
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * @var GetCategoriesChain
     */
    private $chain;

    /**
     * GetCategoriesAction constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
        $this->chain = (new GetCategoriesChain($requestSender))->initiate();
    }

    /**
     * @return Response
     */
    protected function action(): Response
    {
        try {
            $response = $this->chain->run(
                array_merge($this->getRequestBody(true), [
                    'storeId' => $this->request->getHeaderLine('storeId')
                ])
            );
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}