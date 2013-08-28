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
 * Files tree block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Theme_Block_Adminhtml_Wysiwyg_Files_Tree extends Magento_Backend_Block_Template
{
    /**
     * Theme storage
     *
     * @var Magento_Theme_Helper_Storage
     */
    protected $_themeStorage = null;

    /**
     * @param Magento_Theme_Helper_Storage $themeStorage
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Theme_Helper_Storage $themeStorage,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_themeStorage = $themeStorage;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Json source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        return $this->getUrl('*/*/treeJson', $this->_themeStorage->getRequestParams());
    }

    /**
     * Get tree json
     *
     * @param array $data
     * @return string
     */
    public function getTreeJson($data)
    {
        return Zend_Json::encode($data);
    }

    /**
     * Get root node name of tree
     *
     * @return string
     */
    public function getRootNodeName()
    {
        return __('Storage Root');
    }

    /**
     * Return tree node full path based on current path
     *
     * @return string
     */
    public function getTreeCurrentPath()
    {
        $treePath = '/root';
        $path = $this->_themeStorage->getSession()->getCurrentPath();
        if ($path) {
            $path = str_replace($this->_themeStorage->getStorageRoot(), '', $path);
            $relative = '';
            foreach (explode(DIRECTORY_SEPARATOR, $path) as $dirName) {
                if ($dirName) {
                    $relative .= DIRECTORY_SEPARATOR . $dirName;
                    $treePath .= '/' . $this->_themeStorage->urlEncode($relative);
                }
            }
        }
        return $treePath;
    }
}
