<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/04
 * Time: 13:09
 */

namespace App\Application\Actions\User;


use App\Application\Actions\Action;
use App\Application\Actions\Permissions;
use App\Application\Chains\User\BanChain;
use App\Utilities\RequestSenderInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

class BanAction extends Action
{
    private $chain;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger, RequestSenderInterface $requestSender)
    {
        $this->chain = (new BanChain($requestSender, $logger))->initiate();
        $this->logger = $logger;
    }

    /**
     * @return Response
     *
     * @Permissions(operator="and", grants={"ban-user"})
     */
    protected function action(): Response
    {
        try {
            $this->chain->run(
                array_merge(
                    $this->getRequestBody(true),
                    ['userId' => $this->request->getAttribute('userId')]
                )
            );
            $response = ['status' => StatusCodeInterface::STATUS_NO_CONTENT, 'message' => ''];
        } catch (\Throwable $exception) {
            $response = $this->prepareException($exception);
        }

        return $this->respondWithData($response);
    }
}