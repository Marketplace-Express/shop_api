<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/29
 * Time: 01:06
 */

namespace App\Application\Handlers;


use App\Application\Chains\ChainBroken;
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

        return parent::handle($response);
    }
}