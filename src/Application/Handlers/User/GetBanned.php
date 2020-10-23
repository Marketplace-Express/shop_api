<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/16
 * Time: 12:26
 */

namespace App\Application\Handlers\User;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;

class GetBanned extends AbstractHandler
{
    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    /**
     * GetBanned constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    public function handle(array $data = [])
    {
        return $this->requestSender->services->users->getBanned($data['page'], $data['limit']);
    }
}