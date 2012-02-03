<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$session = new Mage_DesignEditor_Model_Session();
$session->deactivateDesignEditor();
Mage::getSingleton('Mage_Adminhtml_Model_Url')->turnOnSecretKey();
