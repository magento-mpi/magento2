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
 * Form element renderer to display composite logo element for VDE
 *
 * @method array getComponents()
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo
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
        //@TODO remove hardcoded control ids

        $components = $this->getComponents();
        $uploaderData = $components['store-name:logo-uploader'];
        $fontData = $components['store-name:font'];

        $this->addField(uniqid('logo-font-'), 'font', array(
            //'title'      => $fontTitle,   //is not used with Mage_Backend_Block_Widget_Form_Renderer_Fieldset_Element
            'components' => $fontData['components'],
            //'label'       => $groupName,
            //'name'        => 'links',
            //'values'      => $files,
        ));

        $uploaderTitle = sprintf('%s {%s: url(%s)}',
            $uploaderData['selector'],
            $uploaderData['attribute'],
            $uploaderData['value']
        );
        $uploaderConfig = array(
            'name'     => 'logo_uploader',
            'title'    => $uploaderTitle,
            //'onclick'  => "return confirm('Are you sure?');",
            //'label'       => $groupName,
            //'values'      => $files,
        );
        $this->addField(uniqid('logo-uploader-'), 'logo-uploader', $uploaderConfig);


    }

    /**
     * Add element types used in composite font element
     */
    public function addElementTypes()
    {
        $this->addType('font', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font');
        $this->addType('logo-uploader', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader');
    }
}

