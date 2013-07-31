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
class Mage_Theme_Block_Adminhtml_Wysiwyg_Files_Content_Uploader extends Magento_Adminhtml_Block_Media_Uploader
{
    /**
     * Path to uploader template
     *
     * @var string
     */
    protected $_template = 'browser/content/uploader.phtml';

    /**
     * Prepare layout
     *
     * @return Magento_Adminhtml_Block_Media_Uploader
     */
    protected function _prepareLayout()
    {
        $this->getConfig()->setUrl(
            $this->getUrl('*/*/upload', $this->helper('Mage_Theme_Helper_Storage')->getRequestParams())
        );
        return parent::_prepareLayout();
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
