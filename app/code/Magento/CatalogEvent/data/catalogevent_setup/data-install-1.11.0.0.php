<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var Magento_CatalogEvent_Model_Resource_Setup $this */

$cmsBlock = array(
    'title'      => 'Catalog Events Lister',
    'identifier' => 'catalog_events_lister',
    'content'    => '{{block class="Magento_CatalogEvent_Block_Event_Lister" name="catalog.event.lister" template="catalogevent/lister.phtml"}}',
    'is_active'  => 1,
    'stores'     => 0,
);

$this->getModelBlockFactory()->create()->setData($cmsBlock)->save();
