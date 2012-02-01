<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Admin
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$session = Mage::getSingleton('Mage_Admin_Model_Session');
$session->unsetData('user');
$session->unsetData('acl');
