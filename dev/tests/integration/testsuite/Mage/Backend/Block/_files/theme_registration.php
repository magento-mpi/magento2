<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  {copyright}
 * @license    {license_link}
 */

/* Point application to predefined layout fixtures */
Mage::getConfig()->setOptions(array(
    'design_dir' => realpath( __DIR__ . '/design'),
));

/** @var $themeRegistration Mage_Core_Model_Theme_Registration */
$themeRegistration = Mage::getObjectManager()->create('Mage_Core_Model_Theme_Registration');
$themeRegistration->register();

Mage::getDesign()->setDesignTheme('test/default', 'adminhtml');

/* Disable loading and saving layout cache */
Mage::app()->getCacheInstance()->banUse('layout');
