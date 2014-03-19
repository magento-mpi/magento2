<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cachePool \Magento\App\Cache\Frontend\Pool */
$cachePool = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\App\Cache\Frontend\Pool');
/** @var $cacheFrontend \Magento\Cache\FrontendInterface */
foreach ($cachePool as $cacheFrontend) {
    $cacheFrontend->getBackend()->save('non-application cache data', 'NON_APPLICATION_FIXTURE', array('SOME_TAG'));
}
