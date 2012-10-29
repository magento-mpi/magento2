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
/** @var $session Mage_DesignEditor_Model_Session */
$session = Mage::getModel('Mage_DesignEditor_Model_Session');
/** @var $auth Mage_Backend_Model_Auth */
$auth = Mage::getModel('Mage_Backend_Model_Auth');
$auth->setAuthStorage($session);
$session->deactivateDesignEditor();
$auth->logout();
$session->unsThemeId();
$session->unsSkin();
/** @var $theme Mage_Core_Model_Theme */
$theme = Mage::getModel('Mage_Core_Model_Theme');
$theme->load($session->getThemeId())->delete();
