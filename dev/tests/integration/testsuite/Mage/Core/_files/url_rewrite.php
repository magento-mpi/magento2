<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
$cmsPageId     = 1;
$rewriteUrl    = 'test_rewrite_path';

// get CMS page
/** @var $cmsPage Mage_Cms_Model_Page */
$cmsPage = $objectManager->create('Mage_Cms_Model_Page');
$cmsPage->load($cmsPageId);
if ($cmsPage->isObjectNew()) {
    $cmsPage->setId($cmsPageId);
    $cmsPage->save();
}

// create URL rewrite
/** @var $rewrite Mage_Core_Model_Url_Rewrite */
$rewrite = $objectManager->create('Mage_Core_Model_Url_Rewrite');
$rewrite->setIdPath('cms_page/' . $cmsPage->getId())
    ->setRequestPath($rewriteUrl)
    ->setTargetPath('cms/page/view/page_id/' . $cmsPage->getId())
    ->save();
