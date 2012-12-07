<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$website = new Mage_Core_Model_Website();
$website->setData(array(
    'code' => 'test_' . uniqid(),
    'name' => 'Test Website' . uniqid(),
    'default_group_id' => 1,
));
return $website;
