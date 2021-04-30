<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/04
 * Time: 13:16
 */

namespace App\Application\Chains;


use App\Application\Handlers\AbstractHandler;

abstract class AbstractChain
{
    /** @var AbstractHandler */
    protected $handlers;

    /**
     * @param array $data
     * @return array|mixed
     */
    public function run(array $data = [])
    {
        return $this->handlers->handle($data);
    }

    abstract public function initiate();
}