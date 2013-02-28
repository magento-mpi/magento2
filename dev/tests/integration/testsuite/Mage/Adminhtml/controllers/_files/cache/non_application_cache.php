<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cachePool Mage_Core_Model_Cache_Frontend_Pool */
$cachePool = Mage::getModel('Mage_Core_Model_Cache_Frontend_Pool');
/** @var $cacheFrontend Magento_Cache_FrontendInterface */
foreach ($cachePool as $cacheFrontend) {
    $cacheFrontend->getBackend()->save('non-application cache data', 'NON_APPLICATION_FIXTURE', array('SOME_TAG'));
}
