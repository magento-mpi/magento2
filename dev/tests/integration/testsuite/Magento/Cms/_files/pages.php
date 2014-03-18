<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $page \Magento\Cms\Model\Page */
$page = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Cms\Model\Page');
$page->setTitle( //->setId(100) // doesn't work: it triggers update
    'Cms Page 100'
)->setIdentifier(
    'page100'
)->setStores(
    array(0)
)->setIsActive(
    1
)->setContent(
    '<h1>Cms Page 100 Title</h1>'
)->setRootTemplate(
    'one_column'
)->save();

$page = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Cms\Model\Page');
$page->setTitle(
    'Cms Page Design Blank'
)->setIdentifier(
    'page_design_blank'
)->setStores(
    array(0)
)->setIsActive(
    1
)->setContent(
    '<h1>Cms Page Design Blank Title</h1>'
)->setRootTemplate(
    'one_column'
)->setCustomTheme(
    'magento_blank'
)->save();
