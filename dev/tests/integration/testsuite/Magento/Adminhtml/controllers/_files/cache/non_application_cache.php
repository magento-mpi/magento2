<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $cachePool \Magento\Core\Model\Cache\Frontend\Pool */
$cachePool = \Mage::getModel('Magento\Core\Model\Cache\Frontend\Pool');
/** @var $cacheFrontend \Magento\Cache\FrontendInterface */
foreach ($cachePool as $cacheFrontend) {
    $cacheFrontend->getBackend()->save('non-application cache data', 'NON_APPLICATION_FIXTURE', array('SOME_TAG'));
}
