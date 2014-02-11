<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Model\Source\Hierarchy\Menu;

/**
 * CMS Hierarchy Navigation Menu source model for list type
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Listtype implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '0'  => __('Unordered'),
            '1' => __('Ordered'),
        );
    }
}
