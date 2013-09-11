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
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Directory\Block\Adminhtml\Frontend\Currency;

class Base extends \Magento\Backend\Block\System\Config\Form\Field
{
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        if ($this->getRequest()->getParam('website') != '') {
            $priceScope = \Mage::app()->getStore()->getConfig(\Magento\Core\Model\Store::XML_PATH_PRICE_SCOPE);
            if ($priceScope == \Magento\Core\Model\Store::PRICE_SCOPE_GLOBAL) {
                return '';
            }
        }
        return parent::render($element);
    }
}
