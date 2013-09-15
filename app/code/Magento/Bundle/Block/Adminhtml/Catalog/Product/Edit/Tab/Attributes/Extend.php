<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle Extended Attribures Block
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Attributes;

class Extend
    extends \Magento\Adminhtml\Block\Catalog\Form\Renderer\Fieldset\Element
{
    const DYNAMIC = 0;
    const FIXED = 1;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Class constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setCanEditPrice(true);
        $this->setCanReadPrice(true);
    }

    /**
     * Get Element Html
     *
     * @return string
     */
    public function getElementHtml()
    {
        $elementHtml = parent::getElementHtml();

        $switchAttributeCode = $this->getAttribute()->getAttributeCode().'_type';
        $switchAttributeValue = $this->getProduct()->getData($switchAttributeCode);

        $html = '<select name="product[' . $switchAttributeCode . ']" id="' . $switchAttributeCode
        . '" type="select" class="required-entry select next-toinput"'
        . ($this->getProduct()->getId() && $this->getAttribute()->getAttributeCode() == 'price'
            || $this->getElement()->getReadonly() ? ' disabled="disabled"' : '') . '>
            <option value="">' . __('-- Select --') . '</option>
            <option ' . ($switchAttributeValue == self::DYNAMIC ? 'selected' : '')
            . ' value="' . self::DYNAMIC . '">' . __('Dynamic') . '</option>
            <option ' . ($switchAttributeValue == self::FIXED ? 'selected' : '')
            . ' value="' . self::FIXED . '">' . __('Fixed') . '</option>
        </select>';

        if (!($this->getAttribute()->getAttributeCode() == 'price'
            && $this->getCanReadPrice() === false)
        ) {
            $html = '<div class="' . $this->getAttribute()->getAttributeCode() .' ">' . $elementHtml . '</div>' . $html;
        }
        if ($this->getDisableChild() && !$this->getElement()->getReadonly()) {
            $html .= "<script type=\"text/javascript\">
                function " . $switchAttributeCode . "_change() {
                    if ($('" . $switchAttributeCode . "').value == '" . self::DYNAMIC . "') {
                        if ($('" . $this->getAttribute()->getAttributeCode() . "')) {
                            $('" . $this->getAttribute()->getAttributeCode() . "').disabled = true;
                            $('" . $this->getAttribute()->getAttributeCode() . "').value = '';
                            $('" . $this->getAttribute()->getAttributeCode() . "').removeClassName('required-entry');
                        }

                        if ($('dynamic-price-warning')) {
                            $('dynamic-price-warning').show();
                        }
                    } else {
                        if ($('" . $this->getAttribute()->getAttributeCode() . "')) {";

            if ($this->getAttribute()->getAttributeCode() == 'price'
                && $this->getCanEditPrice() === false
                && $this->getCanReadPrice() === true
                && $this->getProduct()->isObjectNew()
            ) {
                $defaultProductPrice = ($this->getDefaultProductPrice()) ? $this->getDefaultProductPrice() : "''";
                $html .= "$('" . $this->getAttribute()->getAttributeCode() . "').value = " . $defaultProductPrice . ";";
            } else {
                $html .= "$('" . $this->getAttribute()->getAttributeCode() . "').disabled = false;
                          $('" . $this->getAttribute()->getAttributeCode() . "').addClassName('required-entry');";
            }

            $html .= "}

                        if ($('dynamic-price-warning')) {
                            $('dynamic-price-warning').hide();
                        }
                    }
                }";

            if (!($this->getAttribute()->getAttributeCode() == 'price'
                && !$this->getCanEditPrice()
                && !$this->getProduct()->isObjectNew())
            ) {
                $html .= "$('" . $switchAttributeCode . "').observe('change', " . $switchAttributeCode . "_change);";
            }
            $html .= $switchAttributeCode . "_change();
            </script>";
        }
        return $html;
    }

    public function getProduct()
    {
        if (!$this->getData('product')){
            $this->setData('product', $this->_coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }
}
