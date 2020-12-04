<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/10
 * Time: 13:19
 */

namespace App\Application\Handlers\Category;


use Fig\Http\Message\StatusCodeInterface;
use GraphQL\Mutation;
use GraphQL\Variable;

class DeleteCategory extends AbstractGraphQLHandler
{
    public function handle(array $data = [])
    {
        if (empty($data['storeId'])) {
            throw new \InvalidArgumentException('store id is not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        if (empty($data['category'])) {
            throw new \InvalidArgumentException('category data is not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        $data['userId'] = $data['user']['user_id'];

        $variables = [
            'storeId' => $data['storeId'],
            'category' => $data['category']
        ];

        [$queryVariables, $queryArguments] = $this->appendQueryVariables();

        $mutation = new Mutation('delete');
        $mutation
            ->setVariables($queryVariables)
            ->setArguments($queryArguments)
            ->setOperationName('Mutate');

        $this->requestSender->services->categories->deleteCategory($mutation, $variables);

        return parent::handle($data);
    }

    protected function appendQueryVariables(): array
    {
        return [
            [new Variable('category', 'Delete', true)],
            ['category' => '$category']
        ];
    }
}