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

class Magento_Code_GeneratorTest extends PHPUnit_Framework_TestCase
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
        'factory' => \Magento\Code\Generator\Factory::ENTITY_TYPE,
        'proxy'   => \Magento\Code\Generator\Proxy::ENTITY_TYPE,
        'interceptor' => \Magento\Code\Generator\Interceptor::ENTITY_TYPE,
    );

    /**
     * Model under test
     *
     * @var \Magento\Code\Generator
     */
    protected $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Code\Generator\EntityAbstract
     */
    protected $_generator;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Magento\Autoload\IncludePath
     */
    protected $_autoloader;

    protected function setUp()
    {
        $this->_generator = $this->getMockForAbstractClass('Magento\Code\Generator\EntityAbstract',
            array(), '', true, true, true, array('generate')
        );
        $this->_autoloader = $this->getMock('Magento\Autoload\IncludePath',
            array('getFile'), array(), '', false
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_generator);
        unset($this->_autoloader);
    }

    /**
     * Set generator mock to never call methods
     */
    protected function _prepareGeneratorNeverCalls()
    {
        $this->_generator->expects($this->never())
            ->method('generate');
    }

    public function testGetGeneratedEntities()
    {
        $this->_model = new \Magento\Code\Generator();
        $this->assertEquals(array_values($this->_expectedEntities), $this->_model->getGeneratedEntities());
    }

    /**
     * @dataProvider generateValidClassDataProvider
     */
    public function testGenerateClass($className, $entityType)
    {
        $this->_autoloader->staticExpects($this->once())
            ->method('getFile')
            ->with($className . $entityType)
            ->will($this->returnValue(false));

        $this->_generator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue(true));

        $this->_model = new \Magento\Code\Generator($this->_generator, $this->_autoloader);

        $this->assertEquals(
            \Magento\Code\Generator::GENERATION_SUCCESS,
            $this->_model->generateClass($className . $entityType)
        );
        $this->assertAttributeEmpty('_generator', $this->_model);
    }

    /**
     * @dataProvider generateValidClassDataProvider
     */
    public function testGenerateClassWithExistName($className, $entityType)
    {
        $this->_prepareGeneratorNeverCalls();
        $this->_autoloader->staticExpects($this->once())
            ->method('getFile')
            ->with($className . $entityType)
            ->will($this->returnValue(true));

        $this->_model = new \Magento\Code\Generator($this->_generator, $this->_autoloader);

        $this->assertEquals(
            \Magento\Code\Generator::GENERATION_SKIP,
            $this->_model->generateClass($className . $entityType)
        );
    }

    public function testGenerateClassWithWrongName()
    {
        $this->_prepareGeneratorNeverCalls();
        $this->_autoloader->staticExpects($this->never())
            ->method('getFile');

        $this->_model = new \Magento\Code\Generator($this->_generator, $this->_autoloader);

        $this->assertEquals(
            \Magento\Code\Generator::GENERATION_ERROR,
            $this->_model->generateClass(self::SOURCE_CLASS));
    }

    /**
     * @expectedException \Magento\Exception
     */
    public function testGenerateClassWithError()
    {
        $this->_autoloader->staticExpects($this->once())
            ->method('getFile')
            ->will($this->returnValue(false));

        $this->_generator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue(false));

        $this->_model = new \Magento\Code\Generator($this->_generator, $this->_autoloader);

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
