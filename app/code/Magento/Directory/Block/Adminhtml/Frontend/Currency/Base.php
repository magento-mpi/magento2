<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend model for base currency
 */
namespace Magento\Directory\Block\Adminhtml\Frontend\Currency;

class Base extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        if ($this->getRequest()->getParam('website') != '') {
            $priceScope = $this->_storeManager->getStore()->getConfig(\Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE);
            if ($priceScope == \Magento\Store\Model\Store::PRICE_SCOPE_GLOBAL) {
                return '';
            }
        }
        return parent::render($element);
    }
}
