<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cachePool Magento_Core_Model_Cache_Frontend_Pool */
$cachePool = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Core_Model_Cache_Frontend_Pool');
/** @var $cacheFrontend Magento_Cache_FrontendInterface */
foreach ($cachePool as $cacheFrontend) {
    $cacheFrontend->getBackend()->save('non-application cache data', 'NON_APPLICATION_FIXTURE', array('SOME_TAG'));
}
