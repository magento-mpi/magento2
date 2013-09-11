<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Backend\Model\Config\Source;

class Store implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_options;
    
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = \Mage::getResourceModel('\Magento\Core\Model\Resource\Store\Collection')
                ->load()->toOptionArray();
        }
        return $this->_options;
    }
}
