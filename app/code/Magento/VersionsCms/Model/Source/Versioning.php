<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
