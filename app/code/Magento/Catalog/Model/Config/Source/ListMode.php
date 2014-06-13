<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source;

class ListMode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'grid', 'label' => __('Grid Only')),
            array('value' => 'list', 'label' => __('List Only')),
            array('value' => 'grid-list', 'label' => __('Grid (default) / List')),
            array('value' => 'list-grid', 'label' => __('List (default) / Grid'))
        );
    }
}
