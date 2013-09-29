<?php
/**
 * \Magento\Page\Model\Config\Reader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Page\Model\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Page\Model\Config\Reader
     */
    protected $_model;

    /** @var  \Magento\Config\FileResolverInterface/PHPUnit_Framework_MockObject_MockObject */
    protected $_fileResolverMock;

    public function setUp()
    {
        /** @var $cache \Magento\Core\Model\Cache */
        $cache = \Mage::getModel('Magento\Core\Model\Cache');
        $cache->clean();
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_fileResolverMock = $this->getMockBuilder('Magento\Config\FileResolverInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_model = $objectManager->create('Magento\Page\Model\Config\Reader',
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
