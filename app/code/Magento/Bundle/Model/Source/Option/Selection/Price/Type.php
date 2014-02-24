<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Model\Source\Option\Selection\Price;

/**
 * Extended Attributes Source Model
 *
 * @category   Magento
 * @package    Magento_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Type implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => '0', 'label' => __('Fixed')),
            array('value' => '1', 'label' => __('Percent')),
        );
    }
}
