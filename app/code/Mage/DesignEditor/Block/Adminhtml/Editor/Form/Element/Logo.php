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
    /**
     * Control type
     */
    const CONTROL_TYPE = 'logo';

    /**
     * Add form elements
     *
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo
     */
    protected function _addFields()
    {
        $uploaderData = $this->getComponent('logo-uploader');

        // it was asked to remove font element from store-logo for now
        /*
        $fontData = $this->getComponent('font');
        $fontId = $this->getComponentId('font');
        $this->addField($fontId, 'font', array(
            'components' => $fontData['components'],
            'name'       => $fontId,     //templates not use this, but it used do get components
            'label'      => null
            //'title'      => $fontTitle,   //templates not use this
        ));*/

        $uploaderTitle = sprintf('%s {%s: url(%s)}',
            $uploaderData['selector'],
            $uploaderData['attribute'],
            $uploaderData['value']
        );
        $uploaderId = $this->getComponentId('logo-uploader');
        $this->addField($uploaderId, 'logo-uploader', array(
            'name'     => $uploaderId,
            //'name'     => 'groups[header][fields][logo_src][value]',
            'title'    => $uploaderTitle,
            'label'      => null
        ));

        return $this;
    }

    /**
     * Add element types used in composite font element
     *
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo
     */
    protected function _addElementTypes()
    {
        $this->addType('font', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Font');
        $this->addType('logo-uploader', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader');

        return $this;
    }
}
