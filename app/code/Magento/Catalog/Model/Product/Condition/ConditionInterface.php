<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Condition;

interface ConditionInterface
{
    public function applyToCollection($collection);
    public function getIdsSelect($dbAdapter);
}
