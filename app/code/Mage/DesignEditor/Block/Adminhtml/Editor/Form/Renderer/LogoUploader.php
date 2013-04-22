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
 * @todo Temporary solution.
 * Discuss logo uploader with PO and remove this method.
 * Logo should be assigned to store view level, but not theme.
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
     * Return theme identification number
     *
     * @return int|null
     */
    protected function getThemeId()
    {
        $stagingThemeId = $this->_helperFactory->get('Mage_DesignEditor_Helper_Data')->getEditableThemeId();
        /** @var $helper Mage_Core_Helper_Theme */
        $helper = $this->_helperFactory->get('Mage_Core_Helper_Theme');
        return $helper->getVisibleThemeId($stagingThemeId);
    }

    /**
     * Get logo upload url
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getLogoUploadUrl($store)
    {
        return $this->getUrl('*/system_design_editor_tools/uploadStoreLogo',
            array('theme_id' => $this->getThemeId(), 'store_id' => $store->getId())
        );
    }

    /**
     * Get logo upload url
     *
     * @param Mage_Core_Model_Store $store
     * @return string
     */
    public function getLogoRemoveUrl($store)
    {
        return $this->getUrl('*/system_design_editor_tools/removeStoreLogo',
            array('theme_id' => $this->getThemeId(), 'store_id' => $store->getId())
        );
    }

    /**
     * Get logo image
     *
     * @param Mage_Core_Model_Store $store
     * @return string|null
     */
    public function getLogoImage($store)
    {
        $image = null;
        if (null !== $store) {
            $image = basename($this->_storeConfig->getConfig('design/header/logo_src', $store->getId()));
        }
        return $image;
    }

    /**
     * Get stores list
     *
     * @return Mage_Core_Model_Store|null
     */
    public function getStoresList()
    {
        $stores = Mage::getObjectManager()->get('Mage_Core_Model_Theme_Service')->getStoresByThemes();
        return isset($stores[$this->getThemeId()]) ? $stores[$this->getThemeId()] : null;
    }
}
