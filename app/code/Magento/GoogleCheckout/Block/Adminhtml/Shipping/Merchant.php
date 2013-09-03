<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GoogleCheckout_Block_Adminhtml_Shipping_Merchant
    extends Magento_Backend_Block_System_Config_Form_Field
{
    protected $_addRowButtonHtml = array();
    protected $_removeRowButtonHtml = array();

    protected function _getElementHtml(Magento_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);

        $html = '<div id="merchant_allowed_methods_template" style="display:none">';
        $html .= $this->_getRowTemplateHtml();
        $html .= '</div>';

        $html .= '<ul id="merchant_allowed_methods_container">';
        if ($this->_getValue('method')) {
            foreach ($this->_getValue('method') as $i => $f) {
                if ($i) {
                    $html .= $this->_getRowTemplateHtml($i);
                }
            }
        }
        $html .= '</ul>';
        $html .= $this->_getAddRowButtonHtml('merchant_allowed_methods_container',
            'merchant_allowed_methods_template', __('Add Shipping Method'));

        return $html;
    }

    /**
     * Retrieve html template for shipping method row
     *
     * @param int $rowIndex
     * @return string
     */
    protected function _getRowTemplateHtml($rowIndex = 0)
    {
        $html = '<li>';
        $html .= '<select name="' . $this->getElement()->getName() . '[method][]" ' . $this->_getDisabled() . '>';
        $html .= '<option value="">' . __('* Select shipping method') . '</option>';

        foreach ($this->getShippingMethods() as $carrierCode => $carrier) {
            $html .= '<optgroup label="' . $this->escapeHtml($carrier['title'])
                . '" style="border-top:solid 1px black; margin-top:3px;">';

            foreach ($carrier['methods'] as $methodCode => $method) {
                $code = $carrierCode . '/' . $methodCode;
                $html .= '<option value="' . $this->escapeHtml($code) . '" '
                    . $this->_getSelected('method/' . $rowIndex, $code)
                    . ' style="background:white;">' . $this->escapeHtml($method['title']) . '</option>';
            }
            $html .= '</optgroup>';
        }
        $html .= '</select>';

        $html .= '<div style="margin:5px 0 10px;">';
        $html .= '<label>' . __('Default price:') . '</label> ';
        $html .= '<input class="input-text" style="width:70px;" name="'
            . $this->getElement()->getName() . '[price][]" value="'
            . $this->_getValue('price/' . $rowIndex) . '" ' . $this->_getDisabled() . '/> ';

        $html .= $this->_getRemoveRowButtonHtml();
        $html .= '</div>';
        $html .= '</li>';

        return $html;
    }

    protected function getShippingMethods()
    {
        if (!$this->hasData('shipping_methods')) {
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');

            $storeId = null;
            if (!is_null($website)) {
                $storeId = Mage::getModel('Magento_Core_Model_Website')
                    ->load($website, 'code')
                    ->getDefaultGroup()
                    ->getDefaultStoreId();
            } elseif (!is_null($store)) {
                $storeId = Mage::getModel('Magento_Core_Model_Store')
                    ->load($store, 'code')
                    ->getId();
            }

            $methods = array();
            $carriers = Mage::getSingleton('Magento_Shipping_Model_Config')->getActiveCarriers($storeId);
            foreach ($carriers as $carrierCode=>$carrierModel) {
                if (!$carrierModel->isActive()) {
                    continue;
                }
                $carrierMethods = $carrierModel->getAllowedMethods();
                if (!$carrierMethods) {
                    continue;
                }
                $carrierTitle = $this->_coreStoreConfig->getConfig('carriers/' . $carrierCode . '/title', $storeId);
                $methods[$carrierCode] = array(
                    'title'   => $carrierTitle,
                    'methods' => array(),
                );
                foreach ($carrierMethods as $methodCode=>$methodTitle) {
                    $methods[$carrierCode]['methods'][$methodCode] = array(
                        'title' => '[' . $carrierCode . '] ' . $methodTitle,
                    );
                }
            }
            $this->setData('shipping_methods', $methods);
        }
        return $this->getData('shipping_methods');
    }

    protected function _getDisabled()
    {
        return $this->getElement()->getDisabled() ? ' disabled' : '';
    }

    protected function _getValue($key)
    {
        return $this->getElement()->getData('value/' . $key);
    }

    protected function _getSelected($key, $value)
    {
        return $this->getElement()->getData('value/' . $key) == $value ? 'selected="selected"' : '';
    }

    protected function _getAddRowButtonHtml($container, $template, $title='Add')
    {
        if (!isset($this->_addRowButtonHtml[$container])) {
            $this->_addRowButtonHtml[$container] = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                    ->setType('button')
                    ->setClass('add ' . $this->_getDisabled())
                    ->setLabel(__($title))
                    ->setOnClick("Element.insert($('" . $container . "'), {bottom: $('" . $template . "').innerHTML})")
                    ->setDisabled($this->_getDisabled())
                    ->toHtml();
        }
        return $this->_addRowButtonHtml[$container];
    }

    protected function _getRemoveRowButtonHtml($selector = 'li', $title = 'Remove')
    {
        if (!$this->_removeRowButtonHtml) {
            $this->_removeRowButtonHtml = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
                    ->setType('button')
                    ->setClass('delete v-middle ' . $this->_getDisabled())
                    ->setLabel(__($title))
                    ->setOnClick("Element.remove($(this).up('" . $selector . "'))")
                    ->setDisabled($this->_getDisabled())
                    ->toHtml();
        }
        return $this->_removeRowButtonHtml;
    }
}
