<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Code;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Class name parameter value
     */
    const SOURCE_CLASS = 'testClassName';

    /**
     * Expected generated entities
     *
     * @var array
     */
    protected $_expectedEntities = array(
        'factory' => \Magento\ObjectManager\Code\Generator\Factory::ENTITY_TYPE,
        'proxy' => \Magento\ObjectManager\Code\Generator\Proxy::ENTITY_TYPE,
        'interceptor' => \Magento\Interception\Code\Generator\Interceptor::ENTITY_TYPE
    );

    /**
     * Model under test
     *
     * @var \Magento\Code\Generator
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Autoload\IncludePath
     */
    protected $_autoloader;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Generator\Io
     */
    protected $_ioObjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\App\Filesystem
     */
    protected $_filesystemMock;

    protected function setUp()
    {
        $this->_autoloader = $this->getMock('Magento\Framework\Autoload\IncludePath', array('getFile'), array(), '', false);
        $this->_ioObjectMock = $this->getMockBuilder(
            '\Magento\Code\Generator\Io'
        )->disableOriginalConstructor()->getMock();
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_autoloader);
    }

    public function testGetGeneratedEntities()
    {
        $this->_model = new \Magento\Code\Generator(
            $this->_autoloader,
            $this->_ioObjectMock,
            array('factory', 'proxy', 'interceptor')
        );
        $this->assertEquals(array_values($this->_expectedEntities), $this->_model->getGeneratedEntities());
    }

    /**
     * @expectedException \Magento\Exception
     * @dataProvider generateValidClassDataProvider
     */
    public function testGenerateClass($className, $entityType)
    {
        $this->_autoloader->staticExpects(
            $this->once()
        )->method(
            'getFile'
        )->with(
            $className . $entityType
        )->will(
            $this->returnValue(false)
        );

        $this->_model = new \Magento\Code\Generator(
            $this->_autoloader,
            $this->_ioObjectMock,
            array(
                'factory' => '\Magento\ObjectManager\Code\Generator\Factory',
                'proxy' => '\Magento\ObjectManager\Code\Generator\Proxy',
                'interceptor' => '\Magento\Interception\Code\Generator\Interceptor'
            )
        );

        $this->_model->generateClass($className . $entityType);
    }

    /**
     * @dataProvider generateValidClassDataProvider
     */
    public function testGenerateClassWithExistName($className, $entityType)
    {
        $this->_autoloader->staticExpects(
            $this->once()
        )->method(
            'getFile'
        )->with(
            $className . $entityType
        )->will(
            $this->returnValue(true)
        );

        $this->_model = new \Magento\Code\Generator(
            $this->_autoloader,
            $this->_ioObjectMock,
            array(
                'factory' => '\Magento\ObjectManager\Code\Generator\Factory',
                'proxy' => '\Magento\ObjectManager\Code\Generator\Proxy',
                'interceptor' => '\Magento\Interception\Code\Generator\Interceptor'
            )
        );

        $this->assertEquals(
            \Magento\Code\Generator::GENERATION_SKIP,
            $this->_model->generateClass($className . $entityType)
        );
    }

    public function testGenerateClassWithWrongName()
    {
        $this->_autoloader->staticExpects($this->never())->method('getFile');

        $this->_model = new \Magento\Code\Generator($this->_autoloader, $this->_ioObjectMock);

        $this->assertEquals(
            \Magento\Code\Generator::GENERATION_ERROR,
            $this->_model->generateClass(self::SOURCE_CLASS)
        );
    }

    /**
     * @expectedException \Magento\Exception
     */
    public function testGenerateClassWithError()
    {
        $this->_autoloader->staticExpects($this->once())->method('getFile')->will($this->returnValue(false));

        $this->_model = new \Magento\Code\Generator(
            $this->_autoloader,
            $this->_ioObjectMock,
            array(
                'factory' => '\Magento\ObjectManager\Code\Generator\Factory',
                'proxy' => '\Magento\ObjectManager\Code\Generator\Proxy',
                'interceptor' => '\Magento\Interception\Code\Generator\Interceptor'
            )
        );

        $expectedEntities = array_values($this->_expectedEntities);
        $resultClassName = self::SOURCE_CLASS . ucfirst(array_shift($expectedEntities));

        $this->_model->generateClass($resultClassName);
    }

    /**
     * Data provider for generate class tests
     *
     * @return array
     */
    public function generateValidClassDataProvider()
    {
        $data = array();
        foreach ($this->_expectedEntities as $generatedEntity) {
            $generatedEntity = ucfirst($generatedEntity);
            $data['test class for ' . $generatedEntity] = array(
                'class name' => self::SOURCE_CLASS,
                'entity type' => $generatedEntity
            );
        }
        return $data;
    }
}
