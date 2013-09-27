<?php
/**
 * Magento_Page_Model_Config_Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Page_Model_Config_ReaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Page_Model_Config_Reader
     */
    protected $_model;

    /** @var  Magento_Config_FileResolverInterface/PHPUnit_Framework_MockObject_MockObject */
    protected $_fileResolverMock;

    public function setUp()
    {
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        /** @var $cache Magento_Core_Model_Cache */
        $cache = $objectManager->create('Magento_Core_Model_Cache');
        $cache->clean();
        $this->_fileResolverMock = $this->getMockBuilder('Magento_Config_FileResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = $objectManager->create('Magento_Page_Model_Config_Reader',
            array('fileResolver'=>$this->_fileResolverMock));
    }

    public function testRead()
    {
        $fileList = array(__DIR__ . '/../_files/page_layouts.xml');
        $this->_fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($fileList));
        $result = $this->_model->read('global');
        $expected = array(
            'empty' => array(
                'label' => 'Empty',
                'code' => 'empty',
                'template' => 'empty.phtml',
                'layout_handle' => 'page_empty',
                'is_default' => '0'
            ),
            'one_column' => array(
                'label' => '1 column',
                'code' => 'one_column',
                'template' => '1column.phtml',
                'layout_handle' => 'page_one_column',
                'is_default' => '1'
            ),
        );
        $this->assertEquals($expected, $result);
    }

    public function testMergeCompleteAndPartial()
    {
        $fileList = array(
            __DIR__ . '/../_files/page_layouts.xml',
            __DIR__ . '/../_files/page_layouts2.xml'
        );
        $this->_fileResolverMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($fileList));

        $result = $this->_model->read('global');
        $expected = array(
            'empty' => array(
                'label' => 'Empty',
                'code' => 'empty',
                'template' => 'empty.phtml',
                'layout_handle' => 'page_empty',
                'is_default' => '0'
            ),
            'one_column' => array(
                'label' => '1 column modified',
                'code' => 'one_column',
                'template' => '1column.phtml',
                'layout_handle' => 'page_one_column',
                'is_default' => '1'
            ),
            'two_columns_left' => array(
                'label' => '2 columns with left bar',
                'code' => 'two_columns_left',
                'template' => '2columns-left.phtml',
                'layout_handle' => 'page_two_columns_left',
                'is_default' => '0'
            ),
        );
        $this->assertEquals($expected, $result);
    }
}