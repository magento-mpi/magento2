<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog product downloadable items tab links section
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples extends Mage_Adminhtml_Block_Widget
{
    protected $_config = null;
    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId($this->getId() . '_SamplesUploader');
        $this->setTemplate('downloadable/product/edit/downloadable/samples.phtml');
//        $this->getConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/upload'));
        $this->getConfig()->setUrl(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('downloadable/file/upload', array('type'=>'samples')));
        $this->getConfig()->setParams(array('form_key' => $this->getFormKey()));
        $this->getConfig()->setFileField('samples');
        $this->getConfig()->setFilters(array(
            'images' => array(
                'label' => Mage::helper('adminhtml')->__('Images (.gif, .jpg, .png)'),
                'files' => array('*.gif', '*.jpg', '*.png')
            ),
//            'media' => array(
//                'label' => Mage::helper('adminhtml')->__('Media (.avi, .flv, .swf)'),
//                'files' => array('*.avi', '*.flv', '*.swf')
//            ),
//            'all'    => array(
//                'label' => Mage::helper('adminhtml')->__('All Files'),
//                'files' => array('*.*')
//            )
        ));
    }

    protected function _prepareLayout()
    {
//        $this->setChild(
//            'browse_button',
//            $this->getLayout()->createBlock('adminhtml/widget_button')
//                ->addData(array(
//                    'id'      => $this->_getButtonId('browse'),
//                    'label'   => Mage::helper('adminhtml')->__('Browse Files...'),
//                    'type'    => 'button',
//                    'onclick' => $this->getJsObjectName() . '.browse()'
//                ))
//        );
//
//        $this->setChild(
//            'upload_button',
//            $this->getLayout()->createBlock('adminhtml/widget_button')
//                ->addData(array(
//                    'id'      => $this->_getButtonId('upload'),
//                    'label'   => Mage::helper('adminhtml')->__('Upload Files'),
//                    'type'    => 'button',
//                    'onclick' => $this->getJsObjectName() . '.upload()'
//                ))
//        );

        $this->setChild(
            'delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->addData(array(
                    'id'      => '{{id}}-delete',
                    'class'   => 'delete',
                    'type'    => 'button',
                    'label'   => Mage::helper('adminhtml')->__('Remove'),
                    'onclick' => $this->getJsObjectName() . ".removeFile('{{fileId}}')"
                ))
        );

        return parent::_prepareLayout();
    }

    /**
     * Get model of the product that is being edited
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        $addButton = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('downloadable')->__('Add New Row'),
                'id' => 'add_sample_item',
                'class' => 'add',
            ));
        return $addButton->toHtml();
    }

    public function getSampleData()
    {
        $samplesArr = array();
        $samples = $this->getProduct()->getTypeInstance()->getSamples();
        foreach ($samples as $item) {
            $tmpSampleItem = array(
                'sample_id' => $item->getId(),
                'title' => $item->getTitle(),
                'sample_file' => $item->getSampleFile(),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder()
            );
            if ($this->getProduct() && $item->getStoreTitle()) {
                $tmpSampleItem['store_title'] = $item->getStoreTitle();
            }
            $samplesArr[] = new Varien_Object($tmpSampleItem);
        }

        return $samplesArr;
    }

    public function getUsedDefault()
    {
        return is_null($this->getProduct()->getAttributeDefaultValue('samples_title'));
    }

    public function getSamplesTitle()
    {
        return Mage::getStoreConfig(Mage_Downloadable_Model_Sample::XML_PATH_SAMPLES_TITLE);
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Retrive uploader js object name
     *
     * @return string
     */
    public function getJsObjectName($name='')
    {
        return $this->getHtmlId() . $name . 'JsObject';
    }

    /**
     * Retrive config json
     *
     * @return string
     */
    public function getConfigJson()
    {
        return Zend_Json::encode($this->getConfig()->getData());
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

    public function getFilesJson()
    {
//        if(is_array($this->getElement()->getValue())) {
//            $value = $this->getElement()->getValue();
//            if(count($value['images'])>0) {
//                foreach ($value['images'] as &$image) {
//                    $image['url'] = Mage::getSingleton('catalog/product_media_config')
//                                        ->getMediaUrl($image['file']);
//                }
//                return Zend_Json::encode($value['images']);
//            }
//        }
        return '[]';
    }
}
