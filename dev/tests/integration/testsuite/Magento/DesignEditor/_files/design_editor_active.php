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
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\Config\ScopeInterface'
)->setCurrentScope(
    \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
);
$session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\DesignEditor\Model\Session');
/** @var $auth \Magento\Backend\Model\Auth */
$auth = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Backend\Model\Auth');
$auth->setAuthStorage($session);
$auth->login(\Magento\TestFramework\Bootstrap::ADMIN_NAME, \Magento\TestFramework\Bootstrap::ADMIN_PASSWORD);
$session->activateDesignEditor();

/** @var $theme \Magento\View\Design\ThemeInterface */
$theme = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\View\Design\ThemeInterface');
$theme->setData(
    array(
        'theme_code' => 'blank',
        'area' => 'frontend',
        'parent_id' => null,
        'theme_path' => 'Magento/blank',
        'theme_version' => '2.0.0.0',
        'theme_title' => 'Default',
        'preview_image' => 'media/preview_image.jpg',
        'is_featured' => '0'
    )
);
$theme->save();
$session->setThemeId($theme->getId());
