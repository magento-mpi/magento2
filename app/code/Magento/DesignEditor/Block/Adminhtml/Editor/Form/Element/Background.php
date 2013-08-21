<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display composite background element for VDE
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Background
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'background';

    /**
     * Add form elements
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Background
     */
    protected function _addFields()
    {
        $colorData = $this->getComponent('color-picker');
        $uploaderData = $this->getComponent('background-uploader');

        $colorTitle = $this->_escape(sprintf("%s {%s: %s}",
            $colorData['selector'],
            $colorData['attribute'],
            $colorData['value']
        ));
        $colorHtmlId = $this->getComponentId('color-picker');
        $this->addField($colorHtmlId, 'color-picker', array(
            'name'  => $colorHtmlId,
            'value' => $colorData['value'],
            'title' => $colorTitle,
            'label' => null,
        ));

        $uploaderId = $this->getComponentId('background-uploader');
        $this->addField($uploaderId, 'background-uploader', array(
            'components' => $uploaderData['components'],
            'name'       => $uploaderId,
            'label'      => null
        ));

        return $this;
    }

    /**
     * Add element types used in composite font element
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Background
     */
    protected function _addElementTypes()
    {
        $this->addType('color-picker', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker');
        $this->addType('background-uploader',
            'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader'
        );

        return $this;
    }
}
