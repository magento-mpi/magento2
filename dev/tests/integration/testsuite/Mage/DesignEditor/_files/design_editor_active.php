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
    'parent_id'            => null,
    'theme_path'           => 'default/blank',
    'theme_version'        => '2.0.0.0',
    'theme_title'          => 'Default',
    'preview_image'        => 'media/preview_image.jpg',
    'magento_version_from' => '2.0.0.0-dev1',
    'magento_version_to'   => '*',
    'is_featured'          => '0'
));
$theme->save();
$session->setThemeId($theme->getThemeId());
