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

    /**
     * @var string
     */
    private $dataKey;

    /**
     * Logger constructor.
     * @param LoggerInterface $logger
     * @param string $message
     * @param $dataKey
     */
    public function __construct(LoggerInterface $logger, string $message, $dataKey = null)
    {
        $this->logger = $logger;
        $this->message = $message;
        $this->dataKey = $dataKey;
    }

    public function handle(array $data = [])
    {
        $this->logger->info($this->message);

        if ($this->dataKey) {
            $this->logger->info(json_encode($data[$this->dataKey]));
        }

        return parent::handle($data);
    }
}