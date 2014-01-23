<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * HTML select element block
 *
 * @category   Magento
 * @package    Magento_GiftRegistry
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GiftRegistry\Block\Customer;

class Date extends \Magento\View\Element\Html\Date
{
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Return escaped value
     * Overriding parent method undesired behaviour
     *
     * @return string
     */
    public function getEscapedValue()
    {
        return $this->escapeHtml($this->getValue());
    }
}
