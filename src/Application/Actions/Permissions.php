<?php
/**
 * User: Wajdi Jurry
 * Date: 2020/09/11
 * Time: 12:21
 */

namespace App\Application\Actions;

/**
 * Class Permissions
 * @package App\Http\Controllers\Annotations
 * @Annotation
 */
class Permissions
{
    const OPERATOR_OR = 'or';
    const OPERATOR_AND = 'and';

    /**
     * @var string
     */
    public $operator = self::OPERATOR_AND;

    /**
     * @var array
     */
    public $grants = [];

    /**
     * @var string
     */
    public $policyModel = null;
}