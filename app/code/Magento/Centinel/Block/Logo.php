<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Centinel
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Centinel payment form logo block
 */
namespace Magento\Centinel\Block;

class Logo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'logo.phtml';

    /**
     * Return code of payment method
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getMethod()->getCode();
    }
}
