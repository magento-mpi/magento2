<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rule\Model\Condition;

interface ConditionInterface
{
    /**
     * Get tables to join
     *
     * @return array
     */
    public function getTablesToJoin();

    /**
     * Get field by attribute
     *
     * @return string
     */
    public function getMappedSqlField();

    /**
     * Get argument value to bind
     *
     * @return mixed
     */
    public function getBindArgumentValue();
}
