<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Condition;

use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;

interface ConditionInterface
{
    /**
     * @param AbstractCollection $collection
     * @return $this
     */
    public function applyToCollection($collection);

    /**
     * @param AdapterInterface $dbAdapter
     * @return Select|string
     */
    public function getIdsSelect($dbAdapter);
}
