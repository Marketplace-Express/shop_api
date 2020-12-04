<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/07
 * Time: 14:25
 */

namespace App\Application\Handlers\Category;


use App\Application\Handlers\AbstractHandler;
use App\Utilities\RequestSenderInterface;
use GraphQL\Query;

abstract class AbstractGraphQLHandler extends AbstractHandler
{
    protected $defaultSelections = [
        'id',
        'name',
        'url',
        'order',
        'attributes' => [
            'id',
            'key',
            'name',
            'values'
        ]
    ];

    /**
     * @var RequestSenderInterface
     */
    protected $requestSender;

    /**
     * GetCategories constructor.
     * @param RequestSenderInterface $requestSender
     */
    public function __construct(RequestSenderInterface $requestSender)
    {
        $this->requestSender = $requestSender;
    }

    /**
     * @param Query $query
     * @param array $data
     * @return Query
     */
    protected function buildSelections(Query $query, array $data = [])
    {
        $selectionSet = [];
        foreach ($data as $field => $value) {
            $selectionSet[] = (is_array($value)) ? $this->buildSelections(new Query($field), $value) : $value;
        }

        $query->setSelectionSet($selectionSet);

        return $query;
    }

    abstract protected function appendQueryVariables(): array;
}