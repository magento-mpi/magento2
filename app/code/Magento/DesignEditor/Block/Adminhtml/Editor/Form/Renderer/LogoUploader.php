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
namespace Magento\DesignEditor\Block\Adminhtml\Editor\Form\Renderer;

class LogoUploader
    extends \Magento\DesignEditor\Block\Adminhtml\Editor\Form\Renderer\ImageUploader
{
    /**
     * @var \Magento\DesignEditor\Model\Theme\Context
     */
    protected $_themeContext;

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
     * @param Magento_Core_Helper_Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\DesignEditor\Model\Theme\Context $themeContext
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\DesignEditor\Model\Theme\Context $themeContext,
        array $data = array()
    ) {
        $this->_themeContext = $themeContext;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Get logo upload url
     *
     * @param \Magento\Core\Model\Store $store
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
     * @param \Magento\Core\Model\Store $store
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
     * @param \Magento\Core\Model\Store $store
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
     * @return \Magento\Core\Model\Store|null
     */
    public function getStoresList()
    {
        $stores = \Mage::getObjectManager()->get('Magento\Theme\Model\Config\Customization')->getStoresByThemes();
        return isset($stores[$this->_themeContext->getEditableTheme()->getId()])
            ? $stores[$this->_themeContext->getEditableTheme()->getId()]
            : null;
    }
}
