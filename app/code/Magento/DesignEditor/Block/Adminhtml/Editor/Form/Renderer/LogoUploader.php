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
 * Logo uploader element renderer
 *
 * @todo Temporary solution.
 * Discuss logo uploader with PO and remove this method.
 * Logo should be assigned to store view level, but not theme.
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_LogoUploader
    extends Magento_DesignEditor_Block_Adminhtml_Editor_Form_Renderer_ImageUploader
{
    /**
     * @var Magento_DesignEditor_Model_Theme_Context
     */
    protected $_themeContext;

    /**
     * @var Magento_Theme_Model_Config_Customization
     */
    protected $_customization;

    /**
     * Set of templates to render
     *
     * Upper is rendered first and is inserted into next using <?php echo $this->getHtml() ?>
     *
     * @var array
     */
    protected $_templates = array(
        'Magento_DesignEditor::editor/form/renderer/element/input.phtml',
        'Magento_DesignEditor::editor/form/renderer/logo-uploader.phtml',
    );

    /**
     * Initialize dependencies
     *
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_DesignEditor_Model_Theme_Context $themeContext
     * @param Magento_Theme_Model_Config_Customization $customization
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_DesignEditor_Model_Theme_Context $themeContext,
        Magento_Theme_Model_Config_Customization $customization,
        array $data = array()
    ) {
        $this->_themeContext = $themeContext;
        $this->_customization = $customization;
        parent::__construct($context, $data);
    }

    /**
     * Get logo upload url
     *
     * @param Magento_Core_Model_Store $store
     * @return string
     */
    public function getLogoUploadUrl($store)
    {
        return $this->getUrl('*/system_design_editor_tools/uploadStoreLogo',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId(), 'store_id' => $store->getId())
        );
    }

    /**
     * Get logo upload url
     *
     * @param Magento_Core_Model_Store $store
     * @return string
     */
    public function getLogoRemoveUrl($store)
    {
        return $this->getUrl('*/system_design_editor_tools/removeStoreLogo',
            array('theme_id' => $this->_themeContext->getEditableTheme()->getId(), 'store_id' => $store->getId())
        );
    }

    /**
     * Get logo image
     *
     * @param Magento_Core_Model_Store $store
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
     * @return Magento_Core_Model_Store|null
     */
    public function getStoresList()
    {
        $stores = $this->_customization->getStoresByThemes();
        return isset($stores[$this->_themeContext->getEditableTheme()->getId()])
            ? $stores[$this->_themeContext->getEditableTheme()->getId()]
            : null;
    }
}
