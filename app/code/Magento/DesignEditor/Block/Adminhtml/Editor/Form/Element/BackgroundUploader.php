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
 * Form element renderer to display background uploader element for VDE
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_Composite_Abstract
{
    /**
     * Control type
     */
    const CONTROL_TYPE = 'background-uploader';

    /**
     * Add form elements
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader
     */
    protected function _addFields()
    {
        $uploaderData = $this->getComponent('image-uploader');
        $checkboxData = $this->getComponent('tile');

        $uploaderTitle = $this->_escape(sprintf('%s {%s: url(%s)}',
            $uploaderData['selector'],
            $uploaderData['attribute'],
            $uploaderData['value']
        ));
        $uploaderId = $this->getComponentId('image-uploader');
        $this->addField($uploaderId, 'image-uploader', array(
            'name'     => $uploaderId,
            'title'    => $uploaderTitle,
            'label'    => null,
            'value'    => trim($uploaderData['value']),
        ));

        $checkboxTitle = $this->_escape(sprintf('%s {%s: %s}',
            $checkboxData['selector'],
            $checkboxData['attribute'],
            $checkboxData['value']
        ));
        $checkboxHtmlId = $this->getComponentId('tile');
        $this->addField($checkboxHtmlId, 'checkbox', array(
            'name'    => $checkboxHtmlId,
            'title'   => $checkboxTitle,
            'label'   => 'Tile Background',
            'class'   => 'element-checkbox',
            'value'   => ($checkboxData['value'] == 'disabled') ? 'disabled' : 'repeat',
            'checked' => $checkboxData['value'] == 'repeat'
        ))->setUncheckedValue('no-repeat');

        return $this;
    }

    /**
     * Add element types used in composite font element
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_BackgroundUploader
     */
    protected function _addElementTypes()
    {
        $this->addType('image-uploader', 'Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader');

        return $this;
    }

    /**
     * Get component of 'checkbox' type (actually 'tile')
     *
     * @return Magento_Data_Form_Element_Checkbox
     * @throws Magento_Core_Exception
     */
    public function getCheckboxElement()
    {
        $checkboxId = $this->getComponentId('tile');

        /** @var $element Magento_Data_Form_Element_Abstract */
        foreach ($this->getElements() as $element) {
            if ($element->getData('name') == $checkboxId) {
                return $element;
            }
        }

        throw new Magento_Core_Exception(
            __('Element "%1" is not found in "%2".', $checkboxId, $this->getData('name'))
        );
    }

    /**
     * Get component of 'image-uploader' type
     *
     * @return Magento_DesignEditor_Block_Adminhtml_Editor_Form_Element_ImageUploader
     * @throws Magento_Core_Exception
     */
    public function getImageUploaderElement()
    {
        $imageUploaderId = $this->getComponentId('image-uploader');
        /** @var $e Magento_Data_Form_Element_Abstract */
        foreach ($this->getElements() as $e) {
            if ($e->getData('name') == $imageUploaderId) {
                return $e;
            }
        }
        throw new Magento_Core_Exception(
            __('Element "%1" is not found in "%2".', $imageUploaderId, $this->getData('name'))
        );
    }

    /**
     * Return if this element is available to be displayed.
     *
     * @return bool
     */
    public function isTileAvailable()
    {
        return $this->getCheckboxElement()->getData('value') != 'disabled';
    }
}

