<?php
/**
 * User: Wajdi Jurry
 * Date: 2021/06/13
 * Time: 15:55
 */

namespace App\Application\Chains\User\User;


use App\Application\Chains\AbstractChain;
use App\Application\Handlers\User\User\RefreshToken;
use App\Utilities\RequestSenderInterface;

class RefreshTokenChain extends AbstractChain
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * RefreshTokenChain constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function initiate()
    {
        $this->handlers = new RefreshToken($this->requestSender);

        return $this;
    }
}