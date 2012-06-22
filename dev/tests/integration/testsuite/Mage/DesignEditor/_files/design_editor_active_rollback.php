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
$auth = new Mage_Backend_Model_Auth();
$auth->setAuthStorage($session);
$session->deactivateDesignEditor();
$auth->logout();
