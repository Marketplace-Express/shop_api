<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/14
 * Time: 13:03
 */

namespace App\Application\Handlers;


use Fig\Http\Message\StatusCodeInterface;

class ReturnData extends AbstractHandler
{
    /**
     * @var string
     */
    private $key;

    /**
     * ReturnData constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    public function handle(array $data = [])
    {
        return ['message' => $data[$this->key], 'status' => StatusCodeInterface::STATUS_OK];
    }
}