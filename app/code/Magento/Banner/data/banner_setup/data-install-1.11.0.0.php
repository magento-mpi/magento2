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
 * @var $install \Magento\Banner\Model\Resource\Setup
 */
$install = $this;

$banners = array(
    array(
        'page_top',
        'Free Shipping on All Handbags',
        '<a class="banner callout" href="{{store direct_url="apparel/women/handbags"}}"> '
        . '"Get Free Shipping on All Items under Handbags"</a>'
    ),
    array(
        'page.bottom',
        '15% off Our New Evening Dresses',
        '<a class="banner callout" href="{{store direct_url="apparel/women/evening-dresses"}}"> '
        . '15% off Our New Evening Dresses</a>'
    )
);

/** @var $theme \Magento\View\Design\ThemeInterface */
$theme = $install->getThemeCollection()->getThemeByFullPath('frontend/magento_blank');

foreach ($banners as $sortOrder => $bannerData) {
    $banner = $install->getBannerInstance()
        ->setName($bannerData[1])
        ->setIsEnabled(1)
        ->setStoreContents(array(0 => $bannerData[2]))
        ->save();

    $widgetInstance = $install->getWidgetInstance()
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
            'unique_id'    => $install->getUniqueHash()
        ))
        ->addData(array(
            'instance_type' => 'Magento\Banner\Block\Widget\Banner',
            'theme_id'      => $theme->getId(),
            'title'         => $bannerData[1],
            'sort_order'    => $sortOrder
        ))
        ->save();
}
