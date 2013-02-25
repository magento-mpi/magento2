<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Form element renderer to display composite background element for VDE
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Background
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
{
    const CONTROL_TYPE = 'background';

    /**
     * Add form elements
     */
    protected function _addFields()
    {
        $colorData = $this->getComponent('color-picker');
        $uploaderData = $this->getComponent('background-uploader');

        $colorTitle = sprintf("%s {%s: %s}",
            $colorData['selector'],
            $colorData['attribute'],
            $colorData['value']
        );
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
    }

    /**
     * Add element types used in composite font element
     */
    protected function _addElementTypes()
    {
        $this->addType('color-picker', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker');
        $this->addType('background-uploader',
            'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader'
        );
    }
}
