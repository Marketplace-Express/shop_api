<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/10
 * Time: 15:22
 */

namespace App\Application\Handlers\User;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class UnBanUser extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * UnBan constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        $this->requestSender->services->users->unBan($data['userId']);

        return parent::handle();
    }
}