<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
$cmsPageId     = 1;
$rewriteUrl    = 'test_rewrite_path';

// get CMS page
/** @var $cmsPage \Magento\Cms\Model\Page */
$cmsPage = $objectManager->create('Magento\Cms\Model\Page');
$cmsPage->load($cmsPageId);
if ($cmsPage->isObjectNew()) {
    $cmsPage->setId($cmsPageId);
    $cmsPage->save();
}

// create URL rewrite
/** @var $rewrite \Magento\Core\Model\Url\Rewrite */
$rewrite = $objectManager->create('Magento\Core\Model\Url\Rewrite');
$rewrite->setIdPath('cms_page/' . $cmsPage->getId())
    ->setRequestPath($rewriteUrl)
    ->setTargetPath('cms/page/view/page_id/' . $cmsPage->getId())
    ->save();
