<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/29
 * Time: 01:06
 */

namespace App\Application\Handlers\User\User;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class Register extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * AbstractHandler constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $response = $this->requestSender->services->users->register($data);

        if ($this->next) {
            return parent::handle($response);
        }

        return $response;
    }
}