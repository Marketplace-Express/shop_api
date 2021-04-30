<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/12/15
 * Time: 00:57
 */

namespace App\Application\Handlers;


use Fig\Http\Message\StatusCodeInterface;

class DataHandler extends AbstractHandler
{
    /**
     * @var \Closure
     */
    private $callable;

    /**
     * BeforeHandler constructor.
     * @param \Closure $callable
     */
    public function __construct(\Closure $callable)
    {
        $this->callable = $callable;
    }

    public function handle(array $data = [])
    {
        if (!is_callable($this->callable)) {
            throw new \InvalidArgumentException('callable should be provided', StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }

        return parent::handle(
            call_user_func_array($this->callable, [$data])
        );
    }
}