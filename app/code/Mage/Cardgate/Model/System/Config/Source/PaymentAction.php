<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Mage
 * @package    Mage_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cardgate_Model_System_Config_Source_PaymentAction
{
    /**
     * Helper object
     *
     * @var Mage_Cardgate_Helper_Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param Mage_Cardgate_Helper_Data $helper
     */
    public function __construct(Mage_Cardgate_Helper_Data $helper)
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
                'label' => __('Authorize Only')
            ),
            array(
                'value' => 1,
                'label' => __('Authorize and Capture')
            ),
        );
    }
}
