<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Model\Source\Hierarchy\Menu;

/**
 * CMS Hierarchy Navigation Menu source model for list type
 *
 */
class Listtype implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return ['0' => __('Unordered'), '1' => __('Ordered')];
    }
}
