<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/29
 * Time: 01:07
 */

namespace App\Application\Handlers;


use Psr\Log\LoggerInterface;

class Logger extends AbstractHandler
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $message;

    public function __construct(LoggerInterface $logger, string $message)
    {
        $this->logger = $logger;
        $this->message = $message;
    }

    public function handle(array $data = [])
    {
        $this->logger->info($this->message);
        $this->logger->info(json_encode($data));
        return $data;
    }
}