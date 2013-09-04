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
 * Adminhtml media library uploader
 */
class Magento_Adminhtml_Block_Media_Uploader extends Magento_Adminhtml_Block_Widget
{
    /**
     * @var \Magento\Object
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_template = 'media/uploader.phtml';

    /**
     * @var Magento_Core_Model_View_Url
     */
    protected $_viewUrl;

    /**
     * @var Magento_File_Size
     */
    protected $_fileSizeService;

    /**
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_View_Url $viewUrl
     * @param Magento_File_Size $fileSize
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_View_Url $viewUrl,
        Magento_File_Size $fileSize,
        array $data = array()
    ) {
        $this->_viewUrl = $viewUrl;
        $this->_fileSizeService = $fileSize;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId($this->getId() . '_Uploader');

        $uploadUrl = Mage::getModel('Magento_Backend_Model_Url')->addSessionParam()->getUrl('*/*/upload');
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
     * @return Magento_File_Size
     */
    public function getFileSizeService()
    {
        return $this->_fileSizeService;
    }

    /**
     * Prepares layout and set element renderer
     *
     * @return Magento_Adminhtml_Block_Media_Uploader
     */
    protected function _prepareLayout()
    {
        $head = $this->getLayout()->getBlock('head');
        if ($head) {
            $head->addCss('jquery/fileUploader/css/jquery.fileupload-ui.css');
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrive uploader js object name
     *
     * @return string
     */
    public function getJsObjectName()
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Retrive config json
     *
     * @return string
     */
    public function getConfigJson()
    {
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($this->getConfig()->getData());
    }

    /**
     * Retrive config object
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
