<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Catalog_Product_Helper_Form_BaseimageTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Baseimage
     */
    protected $_model;

    /**
     * @var Mage_Adminhtml_Model_Url
     */
    protected $_url;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @var Mage_Catalog_Model_Product_Media_Config
     */
    protected $_mediaConfig;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helperData;

    protected function setUp()
    {
        $mediaUploader = $this->getMockBuilder('Mage_Adminhtml_Block_Media_Uploader')
            ->disableOriginalConstructor()
            ->setMethods(array('getDataMaxSizeInBytes'))
            ->getMock();
        $this->_url = $this->getMockBuilder('Mage_Adminhtml_Model_Url')
            ->disableOriginalConstructor()
            ->setMethods(array('getUrl'))
            ->getMock();
        $this->_mediaConfig = $this->getMockBuilder('Mage_Catalog_Model_Product_Media_Config')
            ->disableOriginalConstructor()
            ->setMethods(array('getMediaUrl', 'getTmpMediaUrl'))
            ->getMock();
        $this->_design = $this->getMockBuilder('Mage_Core_Model_Design_Package')
            ->disableOriginalConstructor()
            ->setMethods(array('getSkinUrl'))
            ->getMock();
        $this->_helperData = $this->getMockBuilder('Mage_Core_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('escapeHtml'))
            ->getMock();
        $form = $this->getMockBuilder('Varien_Data_Form')
            ->disableOriginalConstructor()
            ->getMock();

        $attributes = array(
            'name' => 'image',
            'label' => "Base Image",
            'mediaUploader' => $mediaUploader,
            'url' => $this->_url,
            'mediaConfig' => $this->_mediaConfig,
            'design' => $this->_design,
            'helperData' => $this->_helperData
        );

        $this->_model = new Mage_Adminhtml_Block_Catalog_Product_Helper_Form_Baseimage($attributes);
        $this->_model->setForm($form);
        $this->_model->setHtmlId('image');
        $this->_url->expects($this->once())->method('getUrl')
            ->will($this->returnValue('http://example.com/pub/images/catalog_product_gallery/upload/'));
    }

    /**
     * Test to get valid html code for 'image' attribute
     *
     * @param mixed $imageValue
     * @param string $methodName
     * @param string $urlPath
     * @dataProvider validateImageUrlDataProvider
     */
    public function testGetElementHtml($imageValue, $methodName, $urlPath)
    {
        $this->_model->setValue($imageValue);
        $this->_helperData->expects($this->once())->method('escapeHtml')->will($this->returnValue($urlPath));
        $this->_mediaConfig->expects($this->once())->method($methodName)->will($this->returnValue($urlPath));
        $html = $this->_createHtmlCode($imageValue, $urlPath);
        $this->assertXmlStringEqualsXmlString("<test>{$html}</test>", "<test>{$this->_model->getElementHtml()}</test>",
            'Another baseimage html code is expected');
    }

    public function validateImageUrlDataProvider()
    {
        return array(
            array(
                '/f/i/file_666.png',
                'getMediaUrl',
                'http://example.com/pub/media/tmp/catalog/product/f/i/file_78.png'
            ),
            array(
                '/f/i/file_666.png.tmp',
                'getTmpMediaUrl',
                'http://example.com/pub/images/image-placeholder.png'
            ),
            array(
                'some_image',
                'getMediaUrl',
                'http://example.com/pub/images/image-placeholder.png'
            )
        );
    }

    /**
     * Test to get valid html code for 'image' with placeholder
     */
    public function testImagePlaceholder()
    {
        $urlPath = 'http://example.com/pub/images/image-placeholder.png';
        $this->_model->setValue(null);
        $this->_design->expects($this->once())->method('getSkinUrl')->will($this->returnValue($urlPath));
        $this->_helperData->expects($this->once())->method('escapeHtml')->will($this->returnValue($urlPath));
        $html = $this->_createHtmlCode('', $urlPath);
        $this->assertXmlStringEqualsXmlString("<test>{$html}</test>", "<test>{$this->_model->getElementHtml()}</test>",
            'Another baseimage html code is expected');
    }

    /**
     * Create html code for expected result
     *
     * @param string $imageValue
     * @param string $urlPath
     *
     * @return string
     */
    protected function _createHtmlCode($imageValue, $urlPath)
    {
        $html = file_get_contents(__DIR__ . '/_files/BaseimageHtml.txt');
        $html = str_replace('%htmlId%', $this->_model->getHtmlId(), $html);
        $html = str_replace('%imageValue%', $imageValue, $html);
        $html = str_replace('%uploadImage%', 'http://example.com/pub/images/catalog_product_gallery/upload/', $html);
        $html = str_replace('%imageUrl%', $urlPath, $html);

        return $html;
    }
}
