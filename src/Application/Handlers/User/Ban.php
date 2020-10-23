<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/04
 * Time: 13:24
 */

namespace App\Application\Handlers\User;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class Ban extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * Ban constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $this->requestSender->services->users->ban($data['userId'], $data['reason'], $data['description']);

        return parent::handle($data);
    }
}