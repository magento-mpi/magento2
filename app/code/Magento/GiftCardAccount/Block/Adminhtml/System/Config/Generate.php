<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\GiftCardAccount\Block\Adminhtml\System\Config;

class Generate extends \Magento\Backend\Block\System\Config\Form\Field
{

    protected $_template = 'config/generate.phtml';

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Return code pool usage
     *
     * @return \Magento\Object
     */
    public function getUsage()
    {
        return \Mage::getModel('\Magento\GiftCardAccount\Model\Pool')->getPoolUsageInfo();
    }
}
