<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Core\Model\Config\Storage\Writer\Db
 */
namespace Magento\Core\Model\Config\Storage\Writer;

class DbTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Storage\Writer\Db
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_resourceMock;


    protected function setUp()
    {
        $this->_resourceMock = $this->getMock('Magento\Core\Model\Resource\Config', array(), array(), '', false, false);
        $this->_model = new \Magento\Core\Model\Config\Storage\Writer\Db($this->_resourceMock);
    }

    protected function tearDown()
    {
        unset($this->_resourceMock);
        unset($this->_model);
    }

    public function testDelete()
    {
        $this->_resourceMock->expects($this->once())
            ->method('deleteConfig')
            ->with('test/path', 'store', 1);
        $this->_model->delete('test/path/', 'store', 1);
    }

    public function testDeleteWithDefaultParams()
    {
        $this->_resourceMock->expects($this->once())
            ->method('deleteConfig')
            ->with('test/path', \Magento\Core\Model\Store::DEFAULT_CODE, 0);
        $this->_model->delete('test/path');
    }

    public function testSave()
    {
        $this->_resourceMock->expects($this->once())
            ->method('saveConfig')
            ->with('test/path', 'test_value', 'store', 1);
        $this->_model->save('test/path/', 'test_value', 'store', 1);
    }

    public function testSaveWithDefaultParams()
    {
        $this->_resourceMock->expects($this->once())
            ->method('saveConfig')
            ->with('test/path', 'test_value', \Magento\Core\Model\Store::DEFAULT_CODE, 0);
        $this->_model->save('test/path', 'test_value');
    }
}
