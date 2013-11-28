<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Block\Html;

/**
 * Html page block
 */
class Welcome extends \Magento\View\Element\Template
{
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Get block message
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_layout->getBlock('header')->getWelcome();
    }
}
