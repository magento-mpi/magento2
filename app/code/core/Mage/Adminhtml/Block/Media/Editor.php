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
 * Adminhtml media library image editor
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Media_Editor extends Mage_Adminhtml_Block_Widget
{

    protected $_config;

    protected $_template = 'media/editor.phtml';

    protected function _construct()
    {
        parent::_construct();

        $this->getConfig()->setParams();
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'rotatecw_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->addData(array(
                    'id'      => $this->_getButtonId('rotatecw'),
                    'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rotate CW'),
                    'onclick' => $this->getJsObjectName() . '.rotateCw()'
                ))
        );

        $this->setChild(
            'rotateccw_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->addData(array(
                    'id'      => $this->_getButtonId('rotateccw'),
                    'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Rotate CCW'),
                    'onclick' => $this->getJsObjectName() . '.rotateCCw()'
                ))
        );

        $this->setChild(
            'resize_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->addData(array(
                    'id'      => $this->_getButtonId('upload'),
                    'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Resize'),
                    'onclick' => $this->getJsObjectName() . '.resize()'
                ))
        );

        $this->setChild(
            'image_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->addData(array(
                    'id'      => $this->_getButtonId('image'),
                    'label'   => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Get Image Base64'),
                    'onclick' => $this->getJsObjectName() . '.getImage()'
                ))
        );

        return parent::_prepareLayout();
    }

    protected function _getButtonId($buttonName)
    {
        return $this->getHtmlId() . '-' . $buttonName;
    }

    public function getRotatecwButtonHtml()
    {
        return $this->getChildHtml('rotatecw_button');
    }

    public function getImageButtonHtml()
    {
        return $this->getChildHtml('image_button');
    }

    public function getRotateccwButtonHtml()
    {
        return $this->getChildHtml('rotateccw_button');
    }

    public function getResizeButtonHtml()
    {
        return $this->getChildHtml('resize_button');
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

}
