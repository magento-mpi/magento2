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

include "attribute_set.php";
/** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
$attributeSet = Magento_Test_Webservice::getFixture('attribute_set_with_configurable');
$simpleProduct = require TESTS_FIXTURES_DIRECTORY . '/_block/Catalog/Product.php';
$simpleProduct->setAttributeSetId($attributeSet->getId());
$simpleProduct->save();
Magento_Test_Webservice::setFixture('product_simple', $simpleProduct);
