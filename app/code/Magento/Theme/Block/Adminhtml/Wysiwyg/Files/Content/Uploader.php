<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files uploader block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Uploader extends Magento_Adminhtml_Block_Media_Uploader
{
    /**
     * Path to uploader template
     *
     * @var string
     */
    protected $_template = 'browser/content/uploader.phtml';

    /**
     * Theme storage
     *
     * @var Magento_Theme_Helper_Storage
     */
    protected $_themeStorage = null;

    /**
     * @param Magento_Theme_Helper_Storage $themeStorage
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param array $data
     */
    public function __construct(
        Magento_Theme_Helper_Storage $themeStorage,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_View_Url $viewUrl,
        array $data = array()
    ) {
        $this->_themeStorage = $themeStorage;
        parent::__construct($context, $viewUrl, $data);
    }

    /**
     * Prepare layout
     *
     * @return Magento_Adminhtml_Block_Media_Uploader
     */
    protected function _prepareLayout()
    {
        $this->getConfig()->setUrl(
            $this->getUrl('*/*/upload', $this->_themeStorage->getRequestParams())
        );
        return parent::_prepareLayout();
    }

    /**
     * Return storage helper
     *
     * @return Magento_Theme_Helper_Storage
     */
    public function getHelperStorage()
    {
        return $this->_themeStorage;
    }
}
