<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     tests
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

include "attribute_set_with_one_attribute.php";

/** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
$attributeSet = Magento_Test_Webservice::getFixture('attribute_set_with_one_attribute');
/** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
$attribute = Magento_Test_Webservice::getFixture('eav_configurable_attribute');
$attributeSourceOptions = $attribute->getSource()->getAllOptions(false);
/** @var $simpleProduct Mage_Catalog_Model_Product */
$simpleProduct = require TEST_FIXTURE_DIR . '/_block/Catalog/Product.php';
$simpleProduct->setAttributeSetId($attributeSet->getId())
    ->setData($attribute->getAttributeCode(), $attributeSourceOptions[0]['value'])
    ->save();
Magento_Test_Webservice::setFixture('simple_product_for_configurable', $simpleProduct);
