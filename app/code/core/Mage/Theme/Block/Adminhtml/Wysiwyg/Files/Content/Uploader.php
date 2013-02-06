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
 * Files uploader block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Uploader extends Mage_Adminhtml_Block_Media_Uploader
{
    /**
     * Change upload url in configuration
     *
     * @return Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Uploader
     */
    protected function _construct()
    {
        parent::_construct();
        $this->getConfig()->setUrl(
            $this->getUrl('*/*/upload', $this->helper('Mage_Theme_Helper_Storage')->getRequestParams())
        );
        return $this;
    }

    /**
     * Return storage helper
     *
     * @return Mage_Theme_Helper_Storage
     */
    public function getHelperStorage()
    {
        return $this->helper('Mage_Theme_Helper_Storage');
    }
}
