<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml media library uploader
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Media_Uploader extends Mage_Adminhtml_Block_Widget
{

    protected $_config;

    protected $_template = 'media/uploader.phtml';

    /**
     * @var Mage_Core_Model_View_Url
     */
    protected $_viewUrl;

    protected function _construct(Mage_Core_Model_View_Url $viewUrl)
    {
        parent::_construct();

        $this->_viewUrl = $viewUrl;

        $this->setId($this->getId() . '_Uploader');

        $this->getConfig()->setUrl(Mage::getModel('Mage_Backend_Model_Url')->addSessionParam()->getUrl('*/*/upload'));
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getConfig()->setFileField('file');
        $this->getConfig()->setFilters(array(
            'images' => array(
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Images (.gif, .jpg, .png)'),
                'files' => array('*.gif', '*.jpg', '*.png')
            ),
            'media' => array(
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Media (.avi, .flv, .swf)'),
                'files' => array('*.avi', '*.flv', '*.swf')
            ),
            'all'    => array(
                'label' => Mage::helper('Mage_Adminhtml_Helper_Data')->__('All Files'),
                'files' => array('*.*')
            )
        ));
    }

    /**
     * Prepares layout and set element renderer
     *
     * @return Mage_Adminhtml_Block_Media_Uploader
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
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($this->getConfig()->getData());
    }

    /**
     * Retrive config object
     *
     * @return Varien_Config
     */
    public function getConfig()
    {
        if(is_null($this->_config)) {
            $this->_config = new Varien_Object();
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
