<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml media library uploader
 */
namespace Magento\Backend\Block\Media;

class Uploader extends \Magento\Backend\Block\Widget
{
    /**
     * @var \Magento\Object
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_template = 'Magento_Backend::media/uploader.phtml';

    /**
     * @var \Magento\File\Size
     */
    protected $_fileSizeService;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\File\Size $fileSize
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\File\Size $fileSize,
        array $data = array()
    ) {
        $this->_fileSizeService = $fileSize;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId($this->getId() . '_Uploader');

        $uploadUrl = $this->_urlBuilder->addSessionParam()->getUrl('adminhtml/*/upload');
        $this->getConfig()->setUrl($uploadUrl);
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getConfig()->setFileField('file');
        $this->getConfig()->setFilters(array(
            'images' => array(
                'label' => __('Images (.gif, .jpg, .png)'),
                'files' => array('*.gif', '*.jpg', '*.png')
            ),
            'media' => array(
                'label' => __('Media (.avi, .flv, .swf)'),
                'files' => array('*.avi', '*.flv', '*.swf')
            ),
            'all' => array(
                'label' => __('All Files'),
                'files' => array('*.*')
            )
        ));
    }

    /**
     * Get file size
     *
     * @return \Magento\File\Size
     */
    public function getFileSizeService()
    {
        return $this->_fileSizeService;
    }

    /**
     * Prepares layout and set element renderer
     *
     * @return \Magento\Backend\Block\Media\Uploader
     */
    protected function _prepareLayout()
    {
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->addChild(
                'jquery-fileUploader-css-jquery-fileupload-ui-css',
                'Magento\Theme\Block\Html\Head\Css',
                array(
                    'file' => 'jquery/fileUploader/css/jquery.fileupload-ui.css'
                )
            );
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve uploader js object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Retrieve config json
     *
     * @return string
     */
    public function getConfigJson()
    {
        return $this->_coreData->jsonEncode($this->getConfig()->getData());
    }

    /**
     * Retrieve config object
     *
     * @return \Magento\Object
     */
    public function getConfig()
    {
        if (null === $this->_config) {
            $this->_config = new \Magento\Object();
        }

        return $this->_config;
    }

    /**
     * Retrieve full uploader SWF's file URL
     * Implemented to solve problem with cross domain SWFs
     * Now uploader can be only in the same URL where backend located
     *
     * @param string $url url to uploader in current theme
     *
     * @return string full URL
     */
    public function getUploaderUrl($url)
    {
        return $this->_viewUrl->getViewFileUrl($url);
    }
}
