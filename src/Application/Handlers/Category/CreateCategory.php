<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/07
 * Time: 14:23
 */

namespace App\Application\Handlers\Category;


use Fig\Http\Message\StatusCodeInterface;
use GraphQL\Mutation;
use GraphQL\Variable;

class CreateCategory extends AbstractGraphQLHandler
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
        $data['selections'] = $data['selections'] ?? $this->defaultSelections;

        $variables = [
            'storeId' => $data['storeId'],
            'userId' => $data['userId'],
            'category' => $data['category']
        ];

        [$queryVariables, $queryArguments] = $this->appendQueryVariables();

        $mutation = new Mutation('create');
        $mutation
            ->setVariables($queryVariables)
            ->setArguments($queryArguments)
            ->setOperationName('Mutate');

        $this->buildSelections($mutation, $data['selections']);

        $response = $this->requestSender->services->categories->createCategory($mutation, $variables);
        $data['category'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }

    /**
     * @return array[]
     */
    protected function appendQueryVariables(): array
    {
        $queryVariables = $queryArguments = [];
        foreach (['category' => 'Create', 'storeId' => 'Uuid', 'userId' => 'Uuid'] as $variable => $type) {
            $queryVariables[$variable] = new Variable($variable, $type, true);
            $queryArguments[$variable] = '$' . $variable;
        }

        return [$queryVariables, $queryArguments];
    }
}