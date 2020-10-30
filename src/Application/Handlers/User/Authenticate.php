<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/29
 * Time: 11:46
 */

namespace App\Application\Handlers\User;


use Fig\Http\Message\StatusCodeInterface;

class Authenticate extends AbstractUserAccess
{
    public function handle(array $data = [])
    {
        $response = $this->requestSender->services->users->isAuthenticated(
            $this->getToken(),
            ['csrf-token' => $this->getCsrfToken()]
        );

        if (empty($response['message']['is_authenticated'])) {
            throw new \Exception('unauthorized', StatusCodeInterface::STATUS_UNAUTHORIZED);
        }

        $data = array_merge($data, ['user' => $response['message']['user']]);

        return parent::handle($data);
    }
}