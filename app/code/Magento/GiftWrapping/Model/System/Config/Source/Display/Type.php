<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Price display type source model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftWrapping\Model\System\Config\Source\Display;

class Type implements \Magento\Framework\Option\ArrayInterface
{
    /**#@+
     * Constants for display type
     */
    const DISPLAY_TYPE_EXCLUDING_TAX = 1;
    const DISPLAY_TYPE_INCLUDING_TAX = 2;
    const DISPLAY_TYPE_BOTH = 3;
    /**#@-*/

    /**
     * @var array
     */
    protected $_options;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();
            $this->_options[] = array(
                'value' => self::DISPLAY_TYPE_EXCLUDING_TAX,
                'label' => __('Excluding Tax')
            );
            $this->_options[] = array(
                'value' => self::DISPLAY_TYPE_INCLUDING_TAX,
                'label' => __('Including Tax')
            );
            $this->_options[] = array(
                'value' => self::DISPLAY_TYPE_BOTH,
                'label' => __('Including and Excluding Tax')
            );
        }
        return $this->_options;
    }
}
