<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog product MAP "Display Actual Price" attribute source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Source\Msrp;

class Type
    extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Display Product Price on gesture
     */
    const TYPE_ON_GESTURE = '1';

    /**
     * Display Product Price in cart
     */
    const TYPE_IN_CART    = '2';

    /**
     * Display Product Price before order confirmation
     */
    const TYPE_BEFORE_ORDER_CONFIRM = '3';

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array(
                    'label' => __('In Cart'),
                    'value' => self::TYPE_IN_CART
                ),
                array(
                    'label' => __('Before Order Confirmation'),
                    'value' => self::TYPE_BEFORE_ORDER_CONFIRM
                ),
                array(
                    'label' => __('On Gesture'),
                    'value' => self::TYPE_ON_GESTURE
                ),
            );
        }
        return $this->_options;
    }

    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
