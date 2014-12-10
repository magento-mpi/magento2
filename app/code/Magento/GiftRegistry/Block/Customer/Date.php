<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftRegistry\Block\Customer;

/**
 * HTML select element block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Date extends \Magento\Framework\View\Element\Html\Date
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, array $data = [])
    {
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
