<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config\Scope')
    ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
$session = \Mage::getModel('Magento\DesignEditor\Model\Session');
/** @var $auth \Magento\Backend\Model\Auth */
$auth = \Mage::getModel('Magento\Backend\Model\Auth');
$auth->setAuthStorage($session);
$auth->login(\Magento\TestFramework\Bootstrap::ADMIN_NAME, \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD);
$session->activateDesignEditor();

/** @var $theme \Magento\Core\Model\Theme */
$theme = \Mage::getModel('Magento\Core\Model\Theme');
$theme->setData(array(
    'theme_code'           => 'blank',
    'area'                 => 'frontend',
    'parent_id'            => null,
    'theme_path'           => 'magento_blank',
    'theme_version'        => '2.0.0.0',
    'theme_title'          => 'Default',
    'preview_image'        => 'media/preview_image.jpg',
    'is_featured'          => '0'
));
$theme->save();
$session->setThemeId($theme->getId());
