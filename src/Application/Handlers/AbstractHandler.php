<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/29
 * Time: 01:04
 */

namespace App\Application\Handlers;


abstract class AbstractHandler implements HandlerInterface
{
    private $next;

    public function next(AbstractHandler $handler): AbstractHandler
    {
        $this->next = $handler;

        return $handler;
    }

    public function handle()
    {
        $args = func_get_args();

        if ($this->next) {
            return call_user_func_array([$this->next, 'handle'], $args);
        }

        return $args;
    }
}