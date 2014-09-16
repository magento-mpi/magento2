<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Search\Adapter\Mysql\Filter\Builder;

use Magento\Framework\App\Resource;
use Magento\Framework\Search\Adapter\Mysql\ConditionManager;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;

class Term implements FilterInterface
{
    const CONDITION_PART_EQUALS = '=';
    const CONDITION_PART_NOT_EQUALS = '!=';
    /**
     * @var ConditionManager
     */
    private $conditionManager;

    /**
     * @param ConditionManager $conditionManager
     */
    public function __construct(
        ConditionManager $conditionManager
    ) {
        $this->conditionManager = $conditionManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilter(
        RequestFilterInterface $filter,
        $isNegation
    ) {
        /** @var \Magento\Framework\Search\Request\Filter\Term $filter */
        return $this->conditionManager->generateCondition(
            $filter->getField(),
            ($isNegation ? self::CONDITION_PART_NOT_EQUALS : self::CONDITION_PART_EQUALS),
            $filter->getValue()
        );
    }
}
