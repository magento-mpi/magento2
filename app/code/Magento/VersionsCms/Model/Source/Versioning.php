<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Source;

/**
 * Versioning configuration source model
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Versioning implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => __('Enabled by Default'),
            '1' => __('Disabled by Default')
        );
    }
}
