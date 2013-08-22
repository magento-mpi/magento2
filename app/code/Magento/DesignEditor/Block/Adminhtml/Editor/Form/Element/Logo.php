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
 * Form element renderer to display composite logo element for VDE
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'logo';

    /**
     * Add form elements
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo
     */
    protected function _addFields()
    {
        $uploaderData = $this->getComponent('logo-uploader');
        $uploaderTitle = $this->_escape(sprintf('%s {%s: url(%s)}',
            $uploaderData['selector'],
            $uploaderData['attribute'],
            $uploaderData['value']
        ));
        $uploaderId = $this->getComponentId('logo-uploader');
        $this->addField($uploaderId, 'logo-uploader', array(
            'name'     => $uploaderId,
            'title'    => $uploaderTitle,
            'label'    => null
        ));

        return $this;
    }

    /**
     * Add element types used in composite font element
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Logo
     */
    protected function _addElementTypes()
    {
        $this->addType('logo-uploader', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_LogoUploader');
        return $this;
    }
}
