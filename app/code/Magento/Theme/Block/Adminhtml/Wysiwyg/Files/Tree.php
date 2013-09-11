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
namespace Magento\Theme\Block\Adminhtml\Wysiwyg\Files;

class Tree extends \Magento\Backend\Block\Template
{
    /**
     * Json source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        return $this->getUrl('*/*/treeJson', $this->helper('Magento\Theme\Helper\Storage')->getRequestParams());
    }

    /**
     * Get tree json
     *
     * @param array $data
     * @return string
     */
    public function getTreeJson($data)
    {
        return \Zend_Json::encode($data);
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
        $path = $this->helper('Magento\Theme\Helper\Storage')->getSession()->getCurrentPath();
        if ($path) {
            $path = str_replace($this->helper('Magento\Theme\Helper\Storage')->getStorageRoot(), '', $path);
            $relative = '';
            foreach (explode(DIRECTORY_SEPARATOR, $path) as $dirName) {
                if ($dirName) {
                    $relative .= DIRECTORY_SEPARATOR . $dirName;
                    $treePath .= '/' . $this->helper('Magento\Theme\Helper\Storage')->urlEncode($relative);
                }
            }
        }
        return $treePath;
    }
}
