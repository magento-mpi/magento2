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
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
{
    const CONTROL_TYPE = 'logo';

    /**
     * Constructor helper
     */
    public function _construct()
    {
        parent::_construct();

        $this->addElementTypes();
        $this->addFields();

        $this->addClass('element-' . self::CONTROL_TYPE);
    }

    /**
     * Add form elements
     */
    public function addFields()
    {
        $uploaderData = $this->getComponent('logo-uploader');
        $fontData = $this->getComponent('font');

        $this->addField($this->getComponentId('font'), 'font', array(
            'components' => $fontData['components'],
            //'title'      => $fontTitle,   //templates not use this
            //'label'       => $groupName,
            //'name'        => 'q123456',     //templates not use this
            //'values'      => $files,
        ));

        $uploaderTitle = sprintf('%s {%s: url(%s)}',
            $uploaderData['selector'],
            $uploaderData['attribute'],
            $uploaderData['value']
        );
        $uploaderId = $this->getComponentId('logo-uploader');
        $uploaderConfig = array(
            'name'     => $uploaderId,
            'title'    => $uploaderTitle,
            //'onclick'  => "return confirm('Are you sure?');",
            //'label'       => $groupName,
            //'values'      => $files,
        );
        $this->addField($uploaderId, 'logo-uploader', $uploaderConfig);
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
