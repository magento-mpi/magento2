<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Catalog_Model_Product_Condition_Interface
{
    public function applyToCollection($collection);
    public function getIdsSelect($dbAdapter);
}
