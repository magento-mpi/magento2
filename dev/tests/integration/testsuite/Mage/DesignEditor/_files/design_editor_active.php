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
$auth->login(Magento_Test_Bootstrap::ADMIN_NAME, Magento_Test_Bootstrap::ADMIN_PASSWORD);
$session->activateDesignEditor();

$theme = new Mage_Core_Model_Theme();
$theme->setData(array(
    'package_code'         => 'default',
    'package_title'        => 'Default',
    'parent_theme'         => 'default',
    'theme_code'           => 'default',
    'theme_version'        => '2.0.0.0',
    'theme_title'          => 'Default',
    'magento_version_from' => '2.0.0.0-dev1',
    'is_featured'          => '0'
));
$theme->save();
$session->setThemeId($theme->getThemeId());
$skin = implode('/', array($theme->getPackageCode(), $theme->getThemeCode(),
                Mage_Core_Model_Design_Package::DEFAULT_SKIN_NAME));
$session->setSkin($skin);
