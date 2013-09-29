<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Uploader block for Wysiwyg Images
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Cms\Wysiwyg\Images\Content;

class Uploader extends \Magento\Adminhtml\Block\Media\Uploader
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Images\Storage
     */
    protected $_imagesStorage;

    /**
     * @param \Magento\Cms\Model\Wysiwyg\Images\Storage $imagesStorage
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\View\Url $viewUrl
     * @param \Magento\File\Size $fileSize
     * @param array $data
     */
    public function __construct(
        \Magento\Cms\Model\Wysiwyg\Images\Storage $imagesStorage,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\View\Url $viewUrl,
        \Magento\File\Size $fileSize,
        array $data = array()
    ) {
        $this->_imagesStorage = $imagesStorage;
        parent::__construct($coreData, $context, $viewUrl, $fileSize, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $type = $this->_getMediaType();
        $allowed = $this->_imagesStorage->getAllowedExtensions($type);
        $labels = array();
        $files = array();
        foreach ($allowed as $ext) {
            $labels[] = '.' . $ext;
            $files[] = '*.' . $ext;
        }
        $this->getConfig()
            ->setUrl($this->_urlBuilder->addSessionParam()->getUrl('*/*/upload', array('type' => $type)))
            ->setFileField('image')
            ->setFilters(array(
                'images' => array(
                    'label' => __('Images (%1)', implode(', ', $labels)),
                    'files' => $files
                )
            ));
    }

    /**
     * Return current media type based on request or data
     * @return string
     */
    protected function _getMediaType()
    {
        if ($this->hasData('media_type')) {
            return $this->_getData('media_type');
        }
        return $this->getRequest()->getParam('type');
    }
}
