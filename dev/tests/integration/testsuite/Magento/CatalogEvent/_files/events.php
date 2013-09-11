<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $eventClosed \Magento\CatalogEvent\Model\Event */
$eventClosed = Mage::getModel('\Magento\CatalogEvent\Model\Event');
$eventClosed
    ->setCategoryId(null)
    ->setDateStart(date('Y-m-d H:i:s', strtotime('-1 year')))
    ->setDateEnd(date('Y-m-d H:i:s', strtotime('-1 month')))
    ->setDisplayState(\Magento\CatalogEvent\Model\Event::DISPLAY_CATEGORY_PAGE)
    ->setSortOrder(30)
    ->setImage('default_website.jpg')
    ->save()
;
$eventClosed
    ->setStoreId(1)
    ->setImage('default_store_view.jpg')
    ->save()
;

/** @var $eventOpen \Magento\CatalogEvent\Model\Event */
$eventOpen = Mage::getModel('\Magento\CatalogEvent\Model\Event');
$eventOpen
    ->setCategoryId(1)
    ->setDateStart(date('Y-m-d H:i:s', strtotime('-1 month')))
    ->setDateEnd(date('Y-m-d H:i:s', strtotime('+1 month')))
    ->setDisplayState(\Magento\CatalogEvent\Model\Event::DISPLAY_PRODUCT_PAGE)
    ->setSortOrder(20)
    ->setImage('default_website.jpg')
    ->save()
;

/** @var $eventUpcoming \Magento\CatalogEvent\Model\Event */
$eventUpcoming = Mage::getModel('\Magento\CatalogEvent\Model\Event');
$eventUpcoming
    ->setCategoryId(2)
    ->setDateStart(date('Y-m-d H:i:s', strtotime('+1 month')))
    ->setDateEnd(date('Y-m-d H:i:s', strtotime('+1 year')))
    ->setDisplayState(
        \Magento\CatalogEvent\Model\Event::DISPLAY_CATEGORY_PAGE
        | \Magento\CatalogEvent\Model\Event::DISPLAY_PRODUCT_PAGE
    )
    ->setSortOrder(10)
    ->setStoreId(1)
    ->setImage('default_store_view.jpg')
    ->save()
;
