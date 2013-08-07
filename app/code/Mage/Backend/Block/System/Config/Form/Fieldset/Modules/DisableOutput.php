<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Block_System_Config_Form_Fieldset_Modules_DisableOutput
    extends Mage_Backend_Block_System_Config_Form_Fieldset
{
    /**
     * @var Magento_Object
     */
    protected $_dummyElement;


    /**
     * @var Mage_Backend_Block_System_Config_Form_Field
     */
    protected $_fieldRenderer;

    /**
     * @var array
     */
    protected $_values;

    /**
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $html = $this->_getHeaderHtml($element);

        $modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());

        $dispatchResult = new Magento_Object($modules);
        $this->_eventManager->dispatch('adminhtml_system_config_advanced_disableoutput_render_before',
            array('modules' => $dispatchResult)
        );
        $modules = $dispatchResult->toArray();

        sort($modules);

        foreach ($modules as $moduleName) {
            if ($moduleName === 'Mage_Adminhtml' || $moduleName === 'Mage_Backend') {
                continue;
            }
            $html.= $this->_getFieldHtml($element, $moduleName);
        }
        $html .= $this->_getFooterHtml($element);

        return $html;
    }

    /**
     * @return Magento_Object
     */
    protected function _getDummyElement()
    {
        if (empty($this->_dummyElement)) {
            $this->_dummyElement = new Magento_Object(array('showInDefault' => 1, 'showInWebsite' => 1));
        }
        return $this->_dummyElement;
    }

    /**
     * @return Mage_Backend_Block_System_Config_Form_Field
     */
    protected function _getFieldRenderer()
    {
        if (empty($this->_fieldRenderer)) {
            $this->_fieldRenderer = Mage::getBlockSingleton('Mage_Backend_Block_System_Config_Form_Field');
        }
        return $this->_fieldRenderer;
    }

    /**
     * @return array
     */
    protected function _getValues()
    {
        if (empty($this->_values)) {
            $this->_values = array(
                array('label' => $this->helper('Mage_Backend_Helper_Data')->__('Enable'), 'value' => 0),
                array('label' => $this->helper('Mage_Backend_Helper_Data')->__('Disable'), 'value' => 1),
            );
        }
        return $this->_values;
    }

    /**
     * @param Magento_Data_Form_Element_Fieldset $fieldset
     * @param string $moduleName
     * @return mixed
     */
    protected function _getFieldHtml($fieldset, $moduleName)
    {
        $configData = $this->getConfigData();
        $path = 'advanced/modules_disable_output/' . $moduleName; //TODO: move as property of form
        if (isset($configData[$path])) {
            $data = $configData[$path];
            $inherit = false;
        } else {
            $data = (int)(string)$this->getForm()->getConfigRoot()->descend($path);
            $inherit = true;
        }

        $element = $this->_getDummyElement();

        $field = $fieldset->addField($moduleName, 'select',
            array(
                'name'          => 'groups[modules_disable_output][fields]['.$moduleName.'][value]',
                'label'         => $moduleName,
                'value'         => $data,
                'values'        => $this->_getValues(),
                'inherit'       => $inherit,
                'can_use_default_value' => $this->getForm()->canUseDefaultValue($element),
                'can_use_website_value' => $this->getForm()->canUseWebsiteValue($element),
            ))->setRenderer($this->_getFieldRenderer());

        return $field->toHtml();
    }
}
