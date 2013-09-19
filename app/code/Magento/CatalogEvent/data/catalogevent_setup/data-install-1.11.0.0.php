<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\CatalogEvent\Model\Resource\Setup $this */

$cmsBlock = array(
    'title'      => 'Catalog Events Lister',
    'identifier' => 'catalog_events_lister',
    'content'    => '{{block class="Magento\CatalogEvent\Block\Event\Lister" name="catalog.event.lister" template="catalogevent/lister.phtml"}}',
    'is_active'  => 1,
    'stores'     => 0,
);

/** @var \Magento\Cms\Model\Block $block */
$block = $this->getBlockFactory()->create();
$block->setData($cmsBlock)->save();
