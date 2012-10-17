<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Tax Rule Edit Form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Tax_Rule_Edit_Form extends Mage_Backend_Block_Widget_Form
{
    /**
     * Init class
     *
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('taxRuleForm');
        $this->setTitle(Mage::helper('Mage_Tax_Helper_Data')->__('Tax Rule Information'));
    }

    /**
     *
     * return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model  = Mage::registry('tax_rule');
        $form   = new Varien_Data_Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset   = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('Mage_Tax_Helper_Data')->__('Tax Rule Information')
        ));

        $rates = Mage::getModel('Mage_Tax_Model_Calculation_Rate')
            ->getCollection()
            ->toOptionArray();

         $fieldset->addField('code', 'text',
            array(
                'name'      => 'code',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Name'),
                'class'     => 'required-entry',
                'required'  => true,
            )
        );

        // Editable multiselect for customer tax class
        $selectConfigJson = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(
            $this->getTaxClassSelectConfig(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER)
        );
        $selectAfterHtml = '<script type="text/javascript">'
            . '/*<![CDATA[*/'
            . '(function($) { $().ready(function () { '
                . "var customerTaxClassMultiselect = new TaxClassEditableMultiselect({$selectConfigJson}); "
                . 'customerTaxClassMultiselect.init(); }); })(jQuery);'
            . '/*]]>*/'
            . '</script>';
        $selectedCustomerTax = $model->getId() ?
            $model->getCustomerTaxClasses() :
            $model->getCustomerTaxClassWithDefault();
        $fieldset->addField($this->getTaxClassSelectHtmlId(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER),
            'multiselect',
            array(
                'name' => $this->getTaxClassSelectHtmlId(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER),
                'label' => Mage::helper('Mage_Tax_Helper_Data')->__('Customer Tax Class'),
                'class' => 'required-entry',
                'values' => $model->getAllOptionsForClass(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER),
                'value' => $selectedCustomerTax,
                'required' => true,
                'after_element_html' => $selectAfterHtml,
            ),
            false,
            true
        );

        // Editable multiselect for product tax class
        $selectConfigJson = Mage::helper('Mage_Core_Helper_Data')->jsonEncode(
            $this->getTaxClassSelectConfig(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT)
        );
        $selectAfterHtml = '<script type="text/javascript">'
            . '/*<![CDATA[*/'
            . '(function($) { $().ready(function () { '
                . "var productTaxClassMultiselect = new TaxClassEditableMultiselect({$selectConfigJson}); "
                . 'productTaxClassMultiselect.init(); }); })(jQuery);'
            . '/*]]>*/'
            . '</script>';
        $selectedProductTax = $model->getId() ?
            $model->getProductTaxClasses() :
            $model->getProductTaxClassWithDefault();
        $fieldset->addField($this->getTaxClassSelectHtmlId(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT), 'multiselect',
            array(
                'name' => $this->getTaxClassSelectHtmlId(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT),
                'label' => Mage::helper('Mage_Tax_Helper_Data')->__('Product Tax Class'),
                'class' => 'required-entry',
                'values' => $model->getAllOptionsForClass(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT),
                'value' => $selectedProductTax,
                'required' => true,
                'after_element_html' => $selectAfterHtml,
            ),
            false,
            true
        );

        $fieldset->addField('tax_rate', 'multiselect',
            array(
                'name'      => 'tax_rate',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Tax Rate'),
                'class'     => 'required-entry',
                'values'    => $rates,
                'value'     => $model->getRates(),
                'required'  => true,
            )
        );
        $fieldset->addField('priority', 'text',
            array(
                'name'      => 'priority',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Priority'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPriority(),
                'required'  => true,
                'note'      => Mage::helper('Mage_Tax_Helper_Data')->__('Tax rates at the same priority are added, others are compounded.'),
            ),
            false,
            true
        );
        $fieldset->addField('position', 'text',
            array(
                'name'      => 'position',
                'label'     => Mage::helper('Mage_Tax_Helper_Data')->__('Sort Order'),
                'class'     => 'validate-not-negative-number',
                'value'     => (int) $model->getPosition(),
                'required'  => true,
            ),
            false,
            true
        );

        if ($model->getId() > 0 ) {
            $fieldset->addField('tax_calculation_rule_id', 'hidden',
                array(
                    'name'      => 'tax_calculation_rule_id',
                    'value'     => $model->getId(),
                    'no_span'   => true
                )
            );
        }

        $fieldset->setAdvancedLabel(Mage::helper('Mage_Tax_Helper_Data')->__('Additional Settings for Tax Rules (collapsed) including Customer & Product Tax Classes'));

        $form->addValues($model->getData());
        $form->setAction($this->getUrl('*/tax_rule/save'));
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrieve HTML element ID for corresponding tax class selector
     *
     * @param string $classType
     * @return string
     */
    public function getTaxClassSelectHtmlId($classType)
    {
        return 'tax_' . strtolower($classType) . '_class';
    }


    /**
     * Retrieve configuration options for tax class editable multiselect
     *
     * @param string $classType
     * @return array
     */
    public function getTaxClassSelectConfig($classType)
    {
        $config = array(
            'class_type' => $classType,
            'new_url' => $this->getUrl('*/tax_class/ajaxSave/'),
            'save_url' => $this->getUrl('*/tax_class/ajaxSave/'),
            'delete_url' => $this->getTaxClassDeleteUrl($classType),
            'delete_confirm_message' => Mage::helper('Mage_Tax_Helper_Data')->__('Do you really want to delete this tax class?'),
            'target_select_id' => $this->getTaxClassSelectHtmlId($classType),
            'add_button_caption' => Mage::helper('Mage_Tax_Helper_Data')->__('Add New Tax Class'),
        );
        return $config;
    }

    /**
     * Retrieve tax class delete URL
     *
     * @param string $classType
     * @return string
     */
    public function getTaxClassDeleteUrl($classType)
    {
        $url = $this->getUrl('*/tax_class_product/ajaxDelete/');
        if ($classType == Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER) {
            $url = $this->getUrl('*/tax_class_customer/ajaxDelete/');
        }
        return $url;
    }

    /**
     * Retrieve Tax Rate delete URL
     *
     * @return string
     */
    public function getTaxRateDeleteUrl()
    {
        return $this->getUrl('*/tax_rate/ajaxDelete/');
    }

    /**
     * Retrieve Tax Rate save URL
     *
     * @return string
     */
    public function getTaxRateSaveUrl()
    {
        return $this->getUrl('*/tax_rate/ajaxSave/');
    }
}
