<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for Bank Transfer payment method form
 */
namespace Magento\OfflinePaymentMethods\Block\Form;

class Banktransfer extends \Magento\Payment\Block\Form
{

    /**
     * Instructions text
     *
     * @var string
     */
    protected $_instructions;

    protected $_template = 'form/banktransfer.phtml';

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
