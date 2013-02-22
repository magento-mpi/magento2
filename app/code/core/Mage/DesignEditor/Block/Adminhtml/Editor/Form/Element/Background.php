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
    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->addElementTypes();
        $this->addFields();
    }

    /**
     * Add form elements
     */
    public function addFields()
    {
        $uploaderData = $this->getComponent('background-uploader', 'image-uploader');
        $checkboxData = $this->getComponent('background-uploader', 'tile');
        $colorData = $this->getComponent('color-picker');

        $colorTitle = sprintf("%s {%s: %s}",
            $colorData['selector'],
            $colorData['attribute'],
            $colorData['value']
        );
        $colorHtmlId = $this->getComponentId('color-picker');
        $this->addField(uniqid('background-color-picker-'), 'color-picker', array(
            'name'  => $colorHtmlId,
            'value' => $colorData['value'],
            'title' => $colorTitle,
            'label' => null,
        ));

        $uploaderTitle = sprintf('%s {%s: url(%s)}',
            $uploaderData['selector'],
            $uploaderData['attribute'],
            $uploaderData['value']
        );
        $uploaderHtmlId = $this->getComponentId('background-uploader');
        $uploaderConfig = array(
            'name'     => $uploaderHtmlId,
            'title'    => $uploaderTitle,
            'label'    => null,
            //'onclick'  => "return confirm('Are you sure?');",
            //'values'   => $files,
        );
        $this->addField($uploaderHtmlId, 'background-uploader', $uploaderConfig);

        $checkboxTitle = sprintf('%s {%s: %s}',
            $checkboxData['selector'],
            $checkboxData['attribute'],
            $checkboxData['value']
        );
        $checkboxHtmlId = $this->getComponentId('tile');
        $this->addField($checkboxHtmlId, 'checkbox', array(
            'name'  => $checkboxHtmlId,
            'title' => $checkboxTitle,
            'label' => 'Tile Background',
        ));
    }

    /**
     * Add element types used in composite font element
     */
    public function addElementTypes()
    {
        $this->addType('color-picker', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ColorPicker');
        $this->addType('background-uploader', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader');
    }
}
