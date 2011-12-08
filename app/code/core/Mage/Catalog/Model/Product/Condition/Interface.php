<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Mage_Catalog_Model_Product_Condition_Interface
{
    public function applyToCollection($collection);
    public function getIdsSelect($dbAdapter);
}
