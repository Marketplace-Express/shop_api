<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/10/02
 * Time: 10:37
 */

namespace App\Application\Services;


use App\Application\Utilities\Connector;
use Psr\Container\ContainerInterface;

class ThrottleService
{
    const KEY_PREFIX = 'throttle';

    /**
     * @var Connector
     */
    private $connector;

    /**
     * @var array
     */
    private $throttleSettings;

    /**
     * ThrottleService constructor.
     * @param Connector $connector
     * @param ContainerInterface $container
     */
    public function __construct(Connector $connector, ContainerInterface $container)
    {
        $this->connector = $connector;
        $this->throttleSettings = $container->get('settings')['api_throttle'];
    }

    /**
     * @param $key
     * @return string
     */
    private function keyBuilder($key): string
    {
        return join(':', [self::KEY_PREFIX, $key]);
    }

    /**
     * @param int $lastActivity
     * @param int $triesPerHour
     * @param int $triesPerDay
     * @return string
     */
    private function setThrottleValue(int $lastActivity, int $triesPerHour, int $triesPerDay): string
    {
        return join(':', [$lastActivity, $triesPerHour, $triesPerDay]);
    }

    /**
     * @param string $key
     * @param string $action
     * @return array
     */
    public function getThrottleValue(string $key, string $action)
    {
        $value = $this->connector->redis->hGet($key, $action);

        return $value ? array_map('intval', explode(':', $value)) : [time(), 0, 0];
    }

    /**
     * @param string $ip
     * @param string $action
     * @return bool
     */
    public function checkThrottle(string $ip, string $action): bool
    {
        if (empty($action) || !isset($this->throttleSettings[$action])) {
            return true;
        }

        $key = $this->keyBuilder($ip);
        [$lastActivity, $triesPerHour, $triesPerDay] = $this->getThrottleValue($key, $action);
        $triesPerHourSetting = $this->throttleSettings[$action][3600];
        $triesPerDaySetting = $this->throttleSettings[$action][86400];

        if ($triesPerDay >= $triesPerDaySetting) {
            return false;
        }

        if (time() - $lastActivity <= 3600 && $triesPerHour >= $triesPerHourSetting) {
            return false;
        }

        return true;
    }

    /**
     * @param string $ip
     * @param string $action
     */
    public function addTry(string $ip, string $action)
    {
        $key = $this->keyBuilder($ip);

        $setExpiry = !$this->connector->redis->exists($key);

        [$lastActivity, $triesPerHour, $triesPerDay] = $this->getThrottleValue($key, $action);

        if (time() - $lastActivity > 3600) {
            $throttle = $this->setThrottleValue(time(), 1, ++$triesPerDay);
        } else {
            $throttle = $this->setThrottleValue($lastActivity, ++$triesPerHour, ++$triesPerDay);
        }

        $this->connector->redis->hSet($key, $action, $throttle);

        if ($setExpiry) {
            $this->connector->redis->expire($key, 86400);
        }
    }
}