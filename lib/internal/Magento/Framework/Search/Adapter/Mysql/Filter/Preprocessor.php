<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter;

use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Framework\Search\Request\FilterInterface;

class Preprocessor implements PreprocessorInterface
{
    /**
     * @var ConditionManager
     */
    private $conditionManager;

    /**
     * @param ConditionManager $conditionManager
     */
    public function __construct(ConditionManager $conditionManager)
    {
        $this->conditionManager = $conditionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function process(FilterInterface $filter, $isNegation, $query)
    {
        return $this->conditionManager->wrapBrackets($query);
    }
}
