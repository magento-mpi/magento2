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
 * Color-picker form element renderer
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_LogoUploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ImageUploader
{
    /**
     * Logo uploader templates
     *
     * @var array
     */
    protected $_templates = array(
        'Mage_DesignEditor::editor/form/renderer/element/input.phtml',
        'Mage_DesignEditor::editor/form/renderer/logo-uploader.phtml',
    );

    /**
     * Get logo upload url
     *
     * @return string
     */
    public function getLogoUploadUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/uploadStoreLogo',
            array('theme_id' => Mage::registry('theme')->getId())
        );
    }

    /**
     * Get logo upload url
     *
     * @return string
     */
    public function getLogoRemoveUrl()
    {
        return $this->getUrl('*/system_design_editor_tools/removeStoreLogo',
            array('theme_id' => Mage::registry('theme')->getId())
        );
    }

    /**
     * Get logo image
     *
     * @return string|bool
     */
    public function getLogoImage()
    {
        /**
         * @todo Temporary solution.
         * Discuss logo uploader with PO and remove this method.
         * Logo should assigned on store view level, but not theme.
         */
        $stores = Mage::getObjectManager()->get('Mage_Core_Model_Theme_Service')->getStoresByThemes();
        if (isset($stores[Mage::registry('theme')->getId()])) {
            $store = reset($stores[Mage::registry('theme')->getId()]);
            return $this->_storeConfig->getConfig('design/header/logo_src', $store->getId());
        }
        return null;
    }
}
