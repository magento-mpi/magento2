<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $installer Magento_Core_Model_Resource_Setup */
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
        'event_type_id' => Magento_Reports_Model_Event::EVENT_PRODUCT_VIEW,
        'event_name'    => 'catalog_product_view'
    ),
    array(
        'event_type_id' => Magento_Reports_Model_Event::EVENT_PRODUCT_SEND,
        'event_name'    => 'sendfriend_product'
    ),
    array(
        'event_type_id' => Magento_Reports_Model_Event::EVENT_PRODUCT_COMPARE,
        'event_name'    => 'catalog_product_compare_add_product'
    ),
    array(
        'event_type_id' => Magento_Reports_Model_Event::EVENT_PRODUCT_TO_CART,
        'event_name'    => 'checkout_cart_add_product'
    ),
    array(
        'event_type_id' => Magento_Reports_Model_Event::EVENT_PRODUCT_TO_WISHLIST,
        'event_name'    => 'wishlist_add_product'
    ),
    array(
        'event_type_id' => Magento_Reports_Model_Event::EVENT_WISHLIST_SHARE,
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
/** @var $cms Magento_Cms_Model_Page */
$cms = Mage::getModel('Magento_Cms_Model_Page')->load('home', 'identifier');

$reportLayoutUpdate    = '<!--<reference name="content">
        <block type="Magento_Catalog_Block_Product_New" name="home.catalog.product.new" alias="product_new" template="product/new.phtml" after="cms_page">
            <action method="addPriceBlockType">
                <type>bundle</type>
                <block>Magento_Bundle_Block_Catalog_Product_Price</block>
                <template>catalog/product/price.phtml</template>
            </action>
        </block>
        <block type="Magento_Reports_Block_Product_Viewed" name="home.reports.product.viewed" alias="product_viewed" template="home_product_viewed.phtml" after="product_new">
            <action method="addPriceBlockType">
                <type>bundle</type>
                <block>Magento_Bundle_Block_Catalog_Product_Price</block>
                <template>catalog/product/price.phtml</template>
            </action>
        </block>
        <block type="Magento_Reports_Block_Product_Compared" name="home.reports.product.compared" template="home_product_compared.phtml" after="product_viewed">
            <action method="addPriceBlockType">
                <type>bundle</type>
                <block>Magento_Bundle_Block_Catalog_Product_Price</block>
                <template>catalog/product/price.phtml</template>
            </action>
        </block>
    </reference>
    <reference name="right">
        <action method="unsetChild"><alias>right.reports.product.viewed</alias></action>
        <action method="unsetChild"><alias>right.reports.product.compared</alias></action>
    </reference>-->';

/*
 * Merge and save old layout update data with report layout data
 */
$cms->setLayoutUpdateXml($cms->getLayoutUpdateXml() . $reportLayoutUpdate)->save();
