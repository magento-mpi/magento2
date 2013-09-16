<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer \Magento\Core\Model\Resource\Setup */
$installer = $this;
/*
 * Prepare database for data upgrade
 */
$installer->startSetup();
/*
 * Report Event Types default data
 */
$eventTypeData = array(
    array(
        'event_type_id' => \Magento\Reports\Model\Event::EVENT_PRODUCT_VIEW,
        'event_name'    => 'catalog_product_view'
    ),
    array(
        'event_type_id' => \Magento\Reports\Model\Event::EVENT_PRODUCT_SEND,
        'event_name'    => 'sendfriend_product'
    ),
    array(
        'event_type_id' => \Magento\Reports\Model\Event::EVENT_PRODUCT_COMPARE,
        'event_name'    => 'catalog_product_compare_add_product'
    ),
    array(
        'event_type_id' => \Magento\Reports\Model\Event::EVENT_PRODUCT_TO_CART,
        'event_name'    => 'checkout_cart_add_product'
    ),
    array(
        'event_type_id' => \Magento\Reports\Model\Event::EVENT_PRODUCT_TO_WISHLIST,
        'event_name'    => 'wishlist_add_product'
    ),
    array(
        'event_type_id' => \Magento\Reports\Model\Event::EVENT_WISHLIST_SHARE,
        'event_name'    => 'wishlist_share'
    )
);

foreach ($eventTypeData as $row) {
    $installer->getConnection()->insertForce($installer->getTable('report_event_types'), $row);
}

/**
 * Prepare database after data upgrade
 */
$installer->endSetup();

/**
 * Cms Page  with 'home' identifier page modification for report pages
 */
/** @var $cms \Magento\Cms\Model\Page */
$cms = \Mage::getModel('Magento\Cms\Model\Page')->load('home', 'identifier');

$reportLayoutUpdate    = '<!--<reference name="content">
        <block class="Magento\Catalog\Block\Product\NewProduct" name="home.catalog.product.new" alias="product_new" template="product/new.phtml" after="cms_page">
            <action method="addPriceBlockType">
                <argument name="type" xsi:type="string">bundle</argument>
                <argument name="block" xsi:type="string">Magento\Bundle\Block\Catalog\Product\Price</argument>
                <argument name="template" xsi:type="string">catalog/product/price.phtml</argument>
            </action>
        </block>
        <block class="Magento\Reports\Block\Product\Viewed" name="home.reports.product.viewed" alias="product_viewed" template="home_product_viewed.phtml" after="product_new">
            <action method="addPriceBlockType">
                <argument name="type" xsi:type="string">bundle</argument>
                <argument name="block" xsi:type="string">Magento\Bundle\Block\Catalog\Product\Price</argument>
                <argument name="template" xsi:type="string">catalog/product/price.phtml</argument>
            </action>
        </block>
        <block class="Magento\Reports\Block\Product\Compared" name="home.reports.product.compared" template="home_product_compared.phtml" after="product_viewed">
            <action method="addPriceBlockType">
                <argument name="type" xsi:type="string">bundle</argument>
                <argument name="block" xsi:type="string">Magento\Bundle\Block\Catalog\Product\Price</argument>
                <argument name="template" xsi:type="string">catalog/product/price.phtml</argument>
            </action>
        </block>
    </reference>
    <reference name="right">
        <action method="unsetChild"><argument name="alias" xsi:type="string">right.reports.product.viewed</argument></action>
        <action method="unsetChild"><argument name="alias" xsi:type="string">right.reports.product.compared</argument></action>
    </reference>-->';

/*
 * Merge and save old layout update data with report layout data
 */
$cms->setLayoutUpdateXml($cms->getLayoutUpdateXml() . $reportLayoutUpdate)->save();
