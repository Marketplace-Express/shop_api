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
    private $printable;

    /**
     * Logger constructor.
     * @param LoggerInterface $logger
     * @param string $message
     * @param $printable
     */
    public function __construct(LoggerInterface $logger, string $message, array $printable = [])
    {
        $this->logger = $logger;
        $this->message = $message;
        $this->printable = $printable;
    }

    public function handle(array $data = [])
    {
        $this->logger->info($this->message);

        if ($this->printable) {
            $printable = $this->printable; $toBePrinted = [];
            array_walk_recursive($data, function ($value, $key) use ($printable, &$toBePrinted) {
                if (in_array($key, $printable)) {
                    $toBePrinted[$key] = $value;
                }
            });
            $this->logger->info(json_encode($toBePrinted));
        }

        return parent::handle($data);
    }
}