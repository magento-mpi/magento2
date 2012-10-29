<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$model = new Mage_Catalog_Model_Resource_Eav_Attribute();
$model->setName('user_attribute')
    ->setId(1)
    ->setEntityTypeId(4)
    ->setIsUserDefined(1);
$model->save();
