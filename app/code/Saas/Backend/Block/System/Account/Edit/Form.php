<?php
/**
 * Backend edit admin user account form
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Backend_Block_System_Account_Edit_Form extends Mage_Adminhtml_Block_System_Account_Edit_Form
{
    /**
     * Locale source model
     *
     * @var Saas_Backend_Model_Config_Source_Locale
     */
    protected  $_locale;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Saas_Backend_Model_Config_Source_Locale_Translated $locale
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Saas_Backend_Model_Config_Source_Locale_Translated $locale,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_locale = $locale;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Saas_Backend_Block_System_Account_Edit_Form
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var Magento_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $this->getForm()->getElement('base_fieldset');
        $element = $fieldset->getElements()->searchById('interface_locale');

        if ($element) {
            $element->setValues($this->_locale->toOptionArray());
        }
        return $this;
    }
}
