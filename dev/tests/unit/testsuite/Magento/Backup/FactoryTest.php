<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backup
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\BackupFactory
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = $this->getMock('Magento\ObjectManager');
        $this->_model = new \Magento\BackupFactory($this->_objectManager);
    }

    /**
     * @expectedException \Magento\Exception
     */
    public function testCreateWrongType()
    {
        $this->_model->create('WRONG_TYPE');
    }

    /**
     * @param string $type
     * @dataProvider allowedTypesDataProvider
     */
    public function testCreate($type)
    {
        $this->_objectManager->expects($this->once())
            ->method('create')
            ->will($this->returnValue('ModelInstance'));

        $this->assertEquals('ModelInstance', $this->_model->create($type));
    }

    /**
     * @return array
     */
    public function allowedTypesDataProvider()
    {
        return array(
            array('db'),
            array('snapshot'),
            array('filesystem'),
            array('media'),
            array('nomedia'),
        );
    }
}
