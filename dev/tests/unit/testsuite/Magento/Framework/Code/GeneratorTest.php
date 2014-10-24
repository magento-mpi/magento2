<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Code;

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
    protected $expectedEntities = array(
        'factory' => \Magento\Framework\ObjectManager\Code\Generator\Factory::ENTITY_TYPE,
        'proxy' => \Magento\Framework\ObjectManager\Code\Generator\Proxy::ENTITY_TYPE,
        'interceptor' => \Magento\Framework\Interception\Code\Generator\Interceptor::ENTITY_TYPE
    );

    /**
     * Model under test
     *
     * @var \Magento\Framework\Code\Generator
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Autoload\IncludePath
     */
    protected $fileResolver;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Generator\Io
     */
    protected $ioObjectMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Framework\Filesystem
     */
    protected $filesystemMock;

    protected function setUp()
    {
        $this->fileResolver = $this->getMock(
            'Magento\Framework\Code\Generator\FileResolver',
            array('getFile'),
            array(),
            '',
            false
        );
        $this->ioObjectMock = $this->getMockBuilder('\Magento\Framework\Code\Generator\Io')
            ->disableOriginalConstructor()
            ->getMock();
    }

    protected function tearDown()
    {
        unset($this->model);
        unset($this->fileResolver);
    }

    public function testGetGeneratedEntities()
    {
        $this->model = new \Magento\Framework\Code\Generator(
            $this->fileResolver,
            $this->ioObjectMock,
            array('factory', 'proxy', 'interceptor')
        );
        $this->assertEquals(array_values($this->expectedEntities), $this->model->getGeneratedEntities());
    }

    /**
     * @expectedException \Magento\Framework\Exception
     * @dataProvider generateValidClassDataProvider
     */
    public function testGenerateClass($className, $entityType)
    {
        $this->fileResolver->expects($this->any())
            ->method('getFile')
            ->with($className . $entityType)
            ->will($this->returnValue(false));

        $this->model = new \Magento\Framework\Code\Generator(
            $this->fileResolver,
            $this->ioObjectMock,
            array(
                'factory' => '\Magento\Framework\ObjectManager\Code\Generator\Factory',
                'proxy' => '\Magento\Framework\ObjectManager\Code\Generator\Proxy',
                'interceptor' => '\Magento\Framework\Interception\Code\Generator\Interceptor'
            )
        );

        $this->model->generateClass($className . $entityType);
    }

    /**
     * @dataProvider generateValidClassDataProvider
     */
    public function testGenerateClassWithExistName($className, $entityType)
    {
        $this->fileResolver->expects($this->once())
            ->method('getFile')
            ->with($className . $entityType)
            ->will($this->returnValue(true));

        $this->model = new \Magento\Framework\Code\Generator(
            $this->fileResolver,
            $this->ioObjectMock,
            array(
                'factory' => '\Magento\Framework\ObjectManager\Code\Generator\Factory',
                'proxy' => '\Magento\Framework\ObjectManager\Code\Generator\Proxy',
                'interceptor' => '\Magento\Framework\Interception\Code\Generator\Interceptor'
            )
        );

        $this->assertEquals(
            \Magento\Framework\Code\Generator::GENERATION_SKIP,
            $this->model->generateClass($className . $entityType)
        );
    }

    public function testGenerateClassWithWrongName()
    {
        $this->fileResolver->expects($this->never())->method('getFile');

        $this->model = new \Magento\Framework\Code\Generator($this->fileResolver, $this->ioObjectMock);

        $this->assertEquals(
            \Magento\Framework\Code\Generator::GENERATION_ERROR,
            $this->model->generateClass(self::SOURCE_CLASS)
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception
     */
    public function testGenerateClassWithError()
    {
        $this->fileResolver->expects($this->once())->method('getFile')->will($this->returnValue(false));

        $this->model = new \Magento\Framework\Code\Generator(
            $this->fileResolver,
            $this->ioObjectMock,
            array(
                'factory' => '\Magento\Framework\ObjectManager\Code\Generator\Factory',
                'proxy' => '\Magento\Framework\ObjectManager\Code\Generator\Proxy',
                'interceptor' => '\Magento\Framework\Interception\Code\Generator\Interceptor'
            )
        );

        $expectedEntities = array_values($this->expectedEntities);
        $resultClassName = self::SOURCE_CLASS . ucfirst(array_shift($expectedEntities));

        $this->model->generateClass($resultClassName);
    }

    /**
     * Data provider for generate class tests
     *
     * @return array
     */
    public function generateValidClassDataProvider()
    {
        $data = array();
        foreach ($this->expectedEntities as $generatedEntity) {
            $generatedEntity = ucfirst($generatedEntity);
            $data['test class for ' . $generatedEntity] = array(
                'class name' => self::SOURCE_CLASS,
                'entity type' => $generatedEntity
            );
        }
        return $data;
    }
}
