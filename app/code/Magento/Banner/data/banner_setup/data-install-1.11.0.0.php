<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @var Enterprise_Banner_Model_Resource_Setup $install
 */
$install = $this;

$banners = array(
    array(
        'top.container',
        'Free Shipping on All Handbags',
        '<a href="{{store direct_url="apparel/women/handbags"}}"> '
            . '<img class="callout" title="Get Free Shipping on All Items under Handbags" '
            . 'src="{{view url="images/callouts/home/free_shipping_all_handbags.jpg"}}" '
            . 'alt="Free Shipping on All Handbags" /></a>'
    ),
    array(
        'footer.before',
        '15% off Our New Evening Dresses',
        '<a href="{{store direct_url="apparel/women/evening-dresses"}}"> '
        . '<img class="callout" title="15% off Our New Evening Dresses" '
        . 'src="{{view url="images/callouts/home/15_off_new_evening_dresses.jpg"}}" '
        . 'alt="15% off Our New Evening Dresses" /></a>'
    )
);

/** @var $theme Magento_Core_Model_Theme */
$theme = Mage::getModel('Magento_Core_Model_Resource_Theme_Collection')
    ->getThemeByFullPath('frontend/magento_fixed_width');

foreach ($banners as $sortOrder => $bannerData) {
    $banner = Mage::getModel('Magento_Banner_Model_Banner')
        ->setName($bannerData[1])
        ->setIsEnabled(1)
        ->setStoreContents(array(0 => $bannerData[2]))
        ->save();

    $widgetInstance = Mage::getModel('Magento_Widget_Model_Widget_Instance')
        ->setData('page_groups', array(
            array(
                'page_group' => 'pages',
                'pages'      => array(
                    'page_id'       => 0,
                    'for'           => 'all',
                    'layout_handle' => 'cms_index_index',
                    'block'         => $bannerData[0],
                    'template'      => 'widget/block.phtml'
            ))
        ))
        ->setData('store_ids', '0')
        ->setData('widget_parameters', array(
            'display_mode' => 'fixed',
            'types'        => array(''),
            'rotate'       => '',
            'banner_ids'   => $banner->getId(),
            'unique_id'    => $install->getCoreData()->uniqHash()
        ))
        ->addData(array(
            'instance_type' => 'Magento_Banner_Block_Widget_Banner',
            'theme_id'      => $theme->getId(),
            'title'         => $bannerData[1],
            'sort_order'    => $sortOrder
        ))
        ->save();
}
