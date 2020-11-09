<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/19
 * Time: 13:54
 */

namespace App\Services;


use App\Utilities\AbstractService;
use App\Utilities\ServiceInterface;

/**
 * Class Categories
 * @package App\Utilities\Services
 */
class Categories extends AbstractService implements ServiceInterface
{
    const SYNC_QUEUE = 'categories_sync';
    const ASYNC_QUEUE = 'categories_async';

    public function getCategories(string $query, array $variables = [])
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE)
            ->setRoute('categories/fetch')
            ->setMethod('post')
            ->setBody([
                'query' => $query,
                'variables' => $variables
            ])
            ->sendSync();
    }

    public function createCategory(string $mutation, array $variables)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE)
            ->setRoute('categories/mutate')
            ->setMethod('post')
            ->setBody([
                'query' => $mutation,
                'variables' => $variables
            ])
            ->sendSync();
    }

    public function updateCategory(string $mutation, array $variables)
    {
        return $this->requestSender
            ->setQueueName(self::SYNC_QUEUE)
            ->setRoute('categories/mutate')
            ->setMethod('put')
            ->setBody([
                'query' => $mutation,
                'variables' => $variables
            ])
            ->sendSync();
    }
}