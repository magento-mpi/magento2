<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Catalog\Model\Config\Source;

class ListMode implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'grid', 'label'=>__('Grid Only')),
            array('value'=>'list', 'label'=>__('List Only')),
            array('value'=>'grid-list', 'label'=>__('Grid (default) / List')),
            array('value'=>'list-grid', 'label'=>__('List (default) / Grid')),
        );
    }
}
