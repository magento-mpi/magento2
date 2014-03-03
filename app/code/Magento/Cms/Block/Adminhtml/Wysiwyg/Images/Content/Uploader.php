<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content;

/**
 * Uploader block for Wysiwyg Images
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Uploader extends \Magento\Backend\Block\Media\Uploader
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Images\Storage
     */
    protected $_imagesStorage;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\File\Size $fileSize
     * @param \Magento\Cms\Model\Wysiwyg\Images\Storage $imagesStorage
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\File\Size $fileSize,
        \Magento\Cms\Model\Wysiwyg\Images\Storage $imagesStorage,
        array $data = array()
    ) {
        $this->_imagesStorage = $imagesStorage;
        parent::__construct($context, $fileSize, $data);
    }

    /**
     * @return void
     */
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
            ->setUrl($this->_urlBuilder->addSessionParam()->getUrl('cms/*/upload', array('type' => $type)))
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
     *
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
