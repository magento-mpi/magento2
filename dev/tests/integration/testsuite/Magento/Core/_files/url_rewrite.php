<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$cmsPageId = 1;
$rewriteUrl = 'test_rewrite_path';

// get CMS page
/** @var $cmsPage \Magento\Cms\Model\Page */
$cmsPage = $objectManager->create('Magento\Cms\Model\Page');
$cmsPage->load($cmsPageId);
if ($cmsPage->isObjectNew()) {
    $cmsPage->setId($cmsPageId);
    $cmsPage->save();
}

return; // @TODO: UrlRewrite
// create URL rewrite
/** @var $rewrite \Magento\UrlRewrite\Model\UrlRewrite */
$rewrite = $objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');
$rewrite->setIdPath(
    'cms_page/' . $cmsPage->getId()
)->setRequestPath(
    $rewriteUrl
)->setTargetPath(
    'cms/page/view/page_id/' . $cmsPage->getId()
)->save();
