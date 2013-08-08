<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Config_Source_Activity_Options
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
     * Return options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            '1' => $this->_helper->__('Active'),
            '0' => $this->_helper->__('Inactive'),
        );
    }
}


