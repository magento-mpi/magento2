<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Mage
 * @package    Magento_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cardgate_Model_System_Config_Source_PaymentAction
{
    /**
     * Helper object
     *
     * @var Magento_Cardgate_Helper_Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param Magento_Cardgate_Helper_Data $helper
     */
    public function __construct(Magento_Cardgate_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Returns payment actions available for CardGate
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 0,
                'label' => $this->_helper->__('Authorize Only')
            ),
            array(
                'value' => 1,
                'label' => $this->_helper->__('Authorize and Capture')
            ),
        );
    }
}
