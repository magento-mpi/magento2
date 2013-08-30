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

/** @var $eventClosed Magento_CatalogEvent_Model_Event */
$eventClosed = Mage::getModel('Magento_CatalogEvent_Model_Event');
$eventClosed
    ->setCategoryId(null)
    ->setDateStart(date('Y-m-d H:i:s', strtotime('-1 year')))
    ->setDateEnd(date('Y-m-d H:i:s', strtotime('-1 month')))
    ->setDisplayState(Magento_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE)
    ->setSortOrder(30)
    ->setImage('default_website.jpg')
    ->save()
;
$eventClosed
    ->setStoreId(1)
    ->setImage('default_store_view.jpg')
    ->save()
;

/** @var $eventOpen Magento_CatalogEvent_Model_Event */
$eventOpen = Mage::getModel('Magento_CatalogEvent_Model_Event');
$eventOpen
    ->setCategoryId(1)
    ->setDateStart(date('Y-m-d H:i:s', strtotime('-1 month')))
    ->setDateEnd(date('Y-m-d H:i:s', strtotime('+1 month')))
    ->setDisplayState(Magento_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE)
    ->setSortOrder(20)
    ->setImage('default_website.jpg')
    ->save()
;

/** @var $eventUpcoming Magento_CatalogEvent_Model_Event */
$eventUpcoming = Mage::getModel('Magento_CatalogEvent_Model_Event');
$eventUpcoming
    ->setCategoryId(2)
    ->setDateStart(date('Y-m-d H:i:s', strtotime('+1 month')))
    ->setDateEnd(date('Y-m-d H:i:s', strtotime('+1 year')))
    ->setDisplayState(
        Magento_CatalogEvent_Model_Event::DISPLAY_CATEGORY_PAGE
        | Magento_CatalogEvent_Model_Event::DISPLAY_PRODUCT_PAGE
    )
    ->setSortOrder(10)
    ->setStoreId(1)
    ->setImage('default_store_view.jpg')
    ->save()
;
