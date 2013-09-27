<?php
/**
 * Magento_Page_Model_Config
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Page_Model_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Page_Model_Config
     */
    protected $_model;

    public function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $cache Magento_Core_Model_Cache */
        $cache = $objectManager->create('Magento_Core_Model_Cache');
        $cache->clean();
        $fileResolverMock = $this->getMockBuilder('Magento_Config_FileResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $configFile = __DIR__ . '/_files/page_layouts.xml';
        $fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(array($configFile)));
        $reader = $objectManager->create('Magento_Page_Model_Config_Reader',
            array('fileResolver'=>$fileResolverMock));
        $data = $objectManager->create('Magento_Page_Model_Config_Data', array('reader'=> $reader));
        $this->_model = $objectManager->create('Magento_Page_Model_Config', array('dataStorage'=>$data));
    }

    public function testGetPageLayouts()
    {
        $empty = array(
            'label' => 'Empty',
            'code' => 'empty',
            'template' => 'empty.phtml',
            'layout_handle' => 'page_empty',
            'is_default' => '0'
        );
        $oneColumn = array(
            'label' => '1 column',
            'code' => 'one_column',
            'template' => '1column.phtml',
            'layout_handle' => 'page_one_column',
            'is_default' => '1'
        );
        $result = $this->_model->getPageLayouts();
        $this->assertEquals($empty, $result['empty']->getData());
        $this->assertEquals($oneColumn, $result['one_column']->getData());
    }

    public function testGetPageLayout()
    {
        $empty = array(
            'label' => 'Empty',
            'code' => 'empty',
            'template' => 'empty.phtml',
            'layout_handle' => 'page_empty',
            'is_default' => '0'
        );
        $this->assertEquals($empty, $this->_model->getPageLayout('empty')->getData());
        $this->assertFalse( $this->_model->getPageLayout('unknownLayoutCode'));
    }

    public function testGetPageLayoutHandles()
    {
        $expected = array(
            'empty' => 'page_empty',
            'one_column' => 'page_one_column',
        );
        $this->assertEquals($expected, $this->_model->getPageLayoutHandles());
    }
}