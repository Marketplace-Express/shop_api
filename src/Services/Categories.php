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

    public function getCategories()
    {
        return $this->requestSender
            ->setQueueName('categories_sync')
            ->setRoute('categories/fetch')
            ->setMethod('post')
            ->setBody([
                'variables' => ['storeId' => 'f58031e2-a1bb-11ea-ac38-0242ac120002'],
                'query' => '{
    categories {
        id
        name
        children {
            id
            name
            children {
                id
                name
                children {
                    id
                    name
                    order
                }
                attributes {
                    id
                    name
                    values
                }
            }
        }
        attributes {
            id
            key
            name
            values
        }
    }
}'
            ])
            ->sendSync();
    }
}