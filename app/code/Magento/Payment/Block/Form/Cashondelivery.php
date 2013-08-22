<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for Cash On Delivery payment method form
 */
class Magento_Payment_Block_Form_Cashondelivery extends Magento_Payment_Block_Form
{

    /**
     * Instructions text
     *
     * @var string
     */
    protected $_instructions;

    protected $_template = 'form/cashondelivery.phtml';

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getMethod()->getInstructions();
        }
        return $this->_instructions;
    }

}
