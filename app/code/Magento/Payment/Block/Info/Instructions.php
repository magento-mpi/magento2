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
 * Block for Bank Transfer payment generic info
 */
namespace Magento\Payment\Block\Info;

class Instructions extends \Magento\Payment\Block\Info
{
    /**
     * Instructions text
     *
     * @var string
     */
    protected $_instructions;

    protected $_template = 'info/instructions.phtml';

    /**
     * Get instructions text from order payment
     * (or from config, if instructions are missed in payment)
     *
     * @return string
     */
    public function getInstructions()
    {
        if (is_null($this->_instructions)) {
            $this->_instructions = $this->getInfo()->getAdditionalInformation('instructions')
                ?: $this->getMethod()->getInstructions();
        }
        return $this->_instructions;
    }
}
