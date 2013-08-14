<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Config_Source_Statuses_Options
    implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * @param Magento_Core_Helper_Abstract $helper
     */
    public function __construct(Magento_Core_Helper_Abstract $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array('0' => $this->_helper->__('Disabled'),
            '1' => $this->_helper->__('Enabled'));
    }
}


