<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $this \Magento\CatalogEvent\Model\Resource\Setup */
$this->addAttribute('quote_item', 'event_id', array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER));
$this->addAttribute('order_item', 'event_id', array('type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER));

$cmsBlock = array(
    'title' => 'Catalog Events Lister',
    'identifier' => 'catalog_events_lister',
    'content' => '{{block class="Magento\\\\CatalogEvent\\\\Block\\\\Event\\\\Lister" name="catalog.event.lister" template="lister.phtml"}}',
    'is_active' => 1,
    'stores' => 0
);

/** @var \Magento\Cms\Model\Block $block */
$block = $this->getBlockFactory()->create();
$block->setData($cmsBlock)->save();
