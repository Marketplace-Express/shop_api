<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/29
 * Time: 01:03
 */

namespace App\Application\Handlers;


interface HandlerInterface
{
    public function next(AbstractHandler $handler): AbstractHandler;

    public function handle();
}