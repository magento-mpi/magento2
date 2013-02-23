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
 * Form element renderer to display background uploader element for VDE
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
{
    const CONTROL_TYPE = 'background-uploader';

    /**
     * Add form elements
     */
    protected function _addFields()
    {
        $uploaderData = $this->getComponent('image-uploader');
        $checkboxData = $this->getComponent('tile');

        $uploaderTitle = sprintf('%s {%s: url(%s)}',
            $uploaderData['selector'],
            $uploaderData['attribute'],
            $uploaderData['value']
        );
        $uploaderId = $this->getComponentId('image-uploader');
        $uploaderConfig = array(
            'name'     => $uploaderId,
            'title'    => $uploaderTitle,
            'label'    => null,
            'value'    => $uploaderData['value'],
        );
        $this->addField($uploaderId, 'image-uploader', $uploaderConfig);

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
            'class' => 'element-'
        ));
    }

    /**
     * Add element types used in composite font element
     */
    protected function _addElementTypes()
    {
        $this->addType('image-uploader', 'Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader');
    }

    /**
     * @return Varien_Data_Form_Element_Checkbox
     * @throws Mage_Core_Exception
     */
    public function getCheckboxElement()
    {
        $checkboxId = $this->getComponentId('tile');

        /** @var $e Varien_Data_Form_Element_Abstract */
        foreach ($this->getElements() as $e) {
            if ($e->getData('name') == $checkboxId) {
                return $e;
            }
        }

        throw new Mage_Core_Exception(
            sprintf('Element "%s" is not found in "%s"', $checkboxId, $this->getData('name'))
        );
    }

    /**
     * @return Mage_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader
     * @throws Mage_Core_Exception
     */
    public function getImageUploaderElement()
    {
        $imageUploaderId = $this->getComponentId('image-uploader');
        /** @var $e Varien_Data_Form_Element_Abstract */
        foreach ($this->getElements() as $e) {
            if ($e->getData('name') == $imageUploaderId) {
                return $e;
            }
        }
        throw new Mage_Core_Exception(
            sprintf('Element "%s" is not found in "%s"', $imageUploaderId, $this->getData('name'))
        );
    }
}

