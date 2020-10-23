<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/19
 * Time: 13:51
 */

namespace App\Utilities;


use App\Services\Stores;

/**
 * Class Services
 * @package App\Utilities\Services
 * @property-read \App\Services\Products $products
 * @property-read \App\Services\Categories $categories
 * @property-read \App\Services\Users $users
 * @property-read Stores $stores
 */
class Services
{
    /**
     * @var \App\Utilities\ServiceInterface[]
     */
    private $services = [];

    /**
     * @var RequestSenderInterface
     */
    private $requestSender;

    public function __construct(RequestSenderInterface $requestSender, array $services = [])
    {
        foreach ($services as $service) {
            $fullyQualifiedClassName = explode('\\', $service);
            $this->services[strtolower(array_pop($fullyQualifiedClassName))] = $service;
        }
        $this->requestSender = $requestSender;
    }

    /**
     * @param $serviceName
     * @return mixed
     * @throws \Exception
     */
    public function __get($serviceName)
    {
        if (isset($this->services[$serviceName])) {
            return new $this->services[$serviceName]($this->requestSender);
        }

        throw new \Exception('service not found');
    }
}