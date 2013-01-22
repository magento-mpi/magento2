<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Theme
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Files tree block
 */
class Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Tree extends Mage_Backend_Block_Template
{
    /**
     * Json source URL
     *
     * @return string
     */
    public function getTreeLoaderUrl()
    {
        $themeId = $this->getRequest()->getParam(Mage_Theme_Helper_Storage::PARAM_THEME_ID);
        $contentType = $this->getRequest()->getParam(Mage_Theme_Helper_Storage::PARAM_CONTENT_TYPE);
        return $this->getUrl('*/*/treeJson', array(
            Mage_Theme_Helper_Storage::PARAM_THEME_ID     => $themeId,
            Mage_Theme_Helper_Storage::PARAM_CONTENT_TYPE => $contentType
        ));
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
        return $this->__('Storage Root');
    }

    /**
     * Return tree node full path based on current path
     *
     * @return string
     */
    public function getTreeCurrentPath()
    {
        $treePath = '/root';
        $helper = Mage::helper('Mage_Theme_Helper_Storage');
        $path = $helper->getSession()->getCurrentPath();
        if ($path) {
            $path = str_replace($helper->getStorageRoot(), '', $path);
            $relative = '';
            foreach (explode(DIRECTORY_SEPARATOR, $path) as $dirName) {
                if ($dirName) {
                    $relative .= DIRECTORY_SEPARATOR . $dirName;
                    $treePath .= '/' . $helper->idEncode($relative);
                }
            }
        }
        return $treePath;
    }
}
