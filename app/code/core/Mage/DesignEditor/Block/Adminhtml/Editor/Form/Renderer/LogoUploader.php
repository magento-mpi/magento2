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
 * Logo uploader element renderer
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_LogoUploader
    extends Mage_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ImageUploader
{
    /**
     * Set of templates to render
     *
     * Upper is rendered first and is inserted into next using <?php echo $this->getHtml() ?>
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
        //@TODO get rid of registry usage

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
        //@TODO get rid of registry usage

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
         * Logo should be assigned to store view level, but not theme.
         * Get rid of registry usage
         */
        $stores = Mage::getObjectManager()->get('Mage_Core_Model_Theme_Service')->getStoresByThemes();
        if (isset($stores[Mage::registry('theme')->getId()])) {
            $store = reset($stores[Mage::registry('theme')->getId()]);
            return $this->_storeConfig->getConfig('design/header/logo_src', $store->getId());
        }
        return null;
    }
}
