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

    private function setThrottleValue(int $tries)
    {
        return join(':', [strtotime('now'), $tries]);
    }

    public function getThrottleValue(string $ip, string $action)
    {
        $value = $this->connector->redis->hGet($this->keyBuilder($ip), $action);

        return $value ? array_map('intval', explode(':', $value)) : [strtotime('now'), 0];
    }

    public function set(string $ip, string $action, int $tries = 0, int $ttl = -1)
    {
        $key = $this->keyBuilder($ip);
        $this->connector->redis->hSet($key, $action, $this->setThrottleValue($tries));
        $this->connector->redis->expire($key, $ttl);
    }

    private function get(string $ip)
    {
        return $this->connector->redis->get($this->keyBuilder($ip));
    }

    /**
     * @param string $ip
     * @param string $action
     * @return bool
     */
    public function checkThrottle(string $ip, string $action): bool
    {
        [$lastActivity, $tries] = $this->getThrottleValue($ip, $action);
        $triesPerHour = $this->throttleSettings[$action][3600];
        $triesPerDay = $this->throttleSettings[$action][86400];

        $now = strtotime('now');

        return ($now - $lastActivity <= 3600 && $tries < $triesPerHour)
            || ((3600 < $now - $lastActivity) && ($now - $lastActivity) <= 86400 && $tries < $triesPerDay);
    }
}