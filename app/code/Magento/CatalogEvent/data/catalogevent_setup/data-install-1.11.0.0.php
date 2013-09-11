<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


$cmsBlock = array(
    'title'         => 'Catalog Events Lister',
    'identifier'    => 'catalog_events_lister',
    'content'       => '{{block class="\Magento\CatalogEvent\Block\Event\Lister" name="catalog.event.lister" template="catalogevent/lister.phtml"}}',
    'is_active'     => 1,
    'stores'        => 0,
);
 \Mage::getModel('\Magento\Cms\Model\Block')->setData($cmsBlock)->save();
