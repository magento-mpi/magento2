<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\VersionsCms\Model\Source;

/**
 * Versioning configuration source model
 *
 */
class Versioning implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return ['1' => __('Enabled by Default'), '1' => __('Disabled by Default')];
    }
}
