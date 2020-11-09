<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/11/06
 * Time: 12:54
 */

namespace App\Application\Handlers\Category;


use Fig\Http\Message\StatusCodeInterface;
use GraphQL\Graph;
use GraphQL\Query;
use GraphQL\Variable;

class GetCategories extends AbstractGraphQLHandler
{
    public function handle(array $data = [])
    {
        $data['categories_ids'] = $data['categories_ids'] ?? [];

        if (empty($data['storeId'])) {
            throw new \InvalidArgumentException('store id is not provided', StatusCodeInterface::STATUS_BAD_REQUEST);
        }

        // Set query variables
        $this->variables = ['storeId' => $data['storeId']];
        foreach ($data['categories_ids'] as $key => $categoryId) {
            $this->variables['id'.$key] = $categoryId;
        }

        [$queryVariables, $queryArguments] = $this->appendQueryInputs($data['categories_ids']);

        // Generate GraphQL query
        $query = (new Query('categories'))
            ->setVariables($queryVariables)
            ->setArguments($queryArguments);

        $data['selections'] = $data['selections'] ?? $this->defaultSelections;

        $this->buildSelections($query, $data['selections']);

        $response = $this->requestSender->services->categories->getCategories($query, $this->variables);
        $data['categories'] = $response['message'];

        if ($this->next) {
            return parent::handle($data);
        }

        return $response;
    }

    /**
     * @param array $inputs
     * @return array[]
     */
    protected function appendQueryInputs(array $inputs = []): array
    {
        $queryVariables = $queryArguments = [];
        foreach ($inputs as $key => $categoryId) {
            $queryVariables[] = new Variable('id'.$key, 'Uuid', true);
            $queryArguments['id'.$key] = '$id'.$key;
        }

        return [$queryVariables, ['ids' => $queryArguments]];
    }
}