<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Price display type source model
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model\System\Config\Source\Tax\Display;

class Type implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_options;

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = array();
            $this->_options[] = array('value'=>\Magento\Tax\Model\Config::DISPLAY_TYPE_EXCLUDING_TAX, 'label'=>__('Excluding Tax'));
            $this->_options[] = array('value'=>\Magento\Tax\Model\Config::DISPLAY_TYPE_INCLUDING_TAX, 'label'=>__('Including Tax'));
            $this->_options[] = array('value'=>\Magento\Tax\Model\Config::DISPLAY_TYPE_BOTH, 'label'=>__('Including and Excluding Tax'));
        }
        return $this->_options;
    }
}
