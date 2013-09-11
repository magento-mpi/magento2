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
namespace Magento\Theme\Block\Adminhtml\Wysiwyg\Files\Content;

class Uploader extends \Magento\Adminhtml\Block\Media\Uploader
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
     * @return \Magento\Adminhtml\Block\Media\Uploader
     */
    protected function _prepareLayout()
    {
        $this->getConfig()->setUrl(
            $this->getUrl('*/*/upload', $this->helper('Magento\Theme\Helper\Storage')->getRequestParams())
        );
        return parent::_prepareLayout();
    }

    /**
     * Return storage helper
     *
     * @return \Magento\Theme\Helper\Storage
     */
    public function getHelperStorage()
    {
        return $this->helper('Magento\Theme\Helper\Storage');
    }
}
