<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/19
 * Time: 18:49
 */

namespace App\Utilities;


abstract class AbstractService
{
    /**
     * @var RequestSenderInterface
     */
    protected $requestSender;

    /**
     * AbstractService constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }
}