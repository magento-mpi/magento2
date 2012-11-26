<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

$applyTo = array_merge(
    explode(',', $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'weight', 'apply_to')),
    array('downloadable')
);

$this->updateAttribute(Mage_Catalog_Model_Product::ENTITY, 'weight', 'apply_to', implode(',', $applyTo));
