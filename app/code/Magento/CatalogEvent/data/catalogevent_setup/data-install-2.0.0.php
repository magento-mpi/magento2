<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\CatalogEvent\Model\Resource\Setup $this */
$this->addAttribute('quote_item', 'event_id', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER]);
$this->addAttribute('order_item', 'event_id', ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER]);

$cmsBlock = [
    'title' => 'Catalog Events Lister',
    'identifier' => 'catalog_events_lister',
    'content' => '{{block class="Magento\\\\CatalogEvent\\\\Block\\\\Event\\\\Lister" name="catalog.event.lister" template="lister.phtml"}}',
    'is_active' => 1,
    'stores' => 0,
];

/** @var \Magento\Cms\Model\Block $block */
$block = $this->getBlockFactory()->create();
$block->setData($cmsBlock)->save();
