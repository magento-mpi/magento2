<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Code
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Code;

require_once __DIR__ . '/GeneratorTest/SourceClassWithNamespace.php';

/**
 * @magentoAppIsolation enabled
 */
require_once __DIR__ . '/GeneratorTest/ParentClassWithNamespace.php';

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    const CLASS_NAME_WITHOUT_NAMESPACE = 'Magento\Code\GeneratorTest\SourceClassWithoutNamespace';
    const CLASS_NAME_WITH_NAMESPACE = 'Magento\Code\GeneratorTest\SourceClassWithNamespace';
    const INTERFACE_NAME_WITHOUT_NAMESPACE = 'Magento\Code\GeneratorTest\SourceInterfaceWithoutNamespace';

    /**
     * @var string
     */
    protected $_includePath;

    /**
     * @var \Magento\Code\Generator
     */
    protected $_generator;

    /**
     * @var \Magento\Code\Generator\Io
     */
    protected $_ioObject;

    protected function setUp()
    {
        $this->_includePath = get_include_path();

        /** @var $dirs \Magento\Core\Model\Dir */
        $dirs = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir');
        $generationDirectory = $dirs->getDir(\Magento\Core\Model\Dir::VAR_DIR) . '/generation';

        \Magento\Autoload\IncludePath::addIncludePath($generationDirectory);

        $this->_ioObject = new \Magento\Code\Generator\Io(
            new \Magento\Io\File(),
            new \Magento\Autoload\IncludePath(),
            $generationDirectory
        );
        $this->_generator = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Code\Generator',
            array('ioObject' => $this->_ioObject)
        );
    }

    protected function tearDown()
    {
        /** @var $dirs \Magento\Core\Model\Dir */
        $dirs = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Dir');
        $generationDirectory = $dirs->getDir(\Magento\Core\Model\Dir::VAR_DIR) . '/generation';
        \Magento\Io\File::rmdirRecursive($generationDirectory);

        set_include_path($this->_includePath);
        unset($this->_generator);
    }

    protected function _clearDocBlock($classBody)
    {
        return preg_replace('/(\/\*[\w\W]*)\nclass/', 'class', $classBody);
    }

    public function testGenerateClassFactoryWithoutNamespace()
    {
        $factoryClassName = self::CLASS_NAME_WITHOUT_NAMESPACE . 'Factory';
        $result = false;
        $generatorResult = $this->_generator->generateClass($factoryClassName);
        // \Magento\Code\Generator will return a skip if the class has already been auto-loaded
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult
            || \Magento\Code\Generator::GENERATION_SKIP == $generatorResult
        ) {
            $result = true;
        }
        $this->assertTrue($result);

        /** @var $factory \Magento\ObjectManager_Factory */
        $factory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($factoryClassName);
        $object = $factory->create();
        $this->assertInstanceOf(self::CLASS_NAME_WITHOUT_NAMESPACE, $object);

        // This test is only valid if the factory created the object if Autoloader did not pick it up automatically
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult) {
            $content = $this->_clearDocBlock(
                file_get_contents(
                    $this->_ioObject->getResultFileName(
                        self::CLASS_NAME_WITHOUT_NAMESPACE . 'Factory'
                    )
                )
            );
            $expectedContent = $this->_clearDocBlock(
                file_get_contents(
                    __DIR__ . '/GeneratorTest/SourceClassWithoutNamespaceFactory.php'
                )
            );
            $this->assertEquals($expectedContent, $content);
        }
    }

    public function testGenerateClassFactoryWithNamespace()
    {
        $factoryClassName = self::CLASS_NAME_WITH_NAMESPACE . 'Factory';
        $result = false;
        $generatorResult = $this->_generator->generateClass($factoryClassName);
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult
            || \Magento\Code\Generator::GENERATION_SKIP == $generatorResult
        ) {
            $result = true;
        }
        $this->assertTrue($result);

        /** @var $factory \Magento\ObjectManager_Factory */
        $factory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($factoryClassName);

        $object = $factory->create();
        $this->assertInstanceOf(self::CLASS_NAME_WITH_NAMESPACE, $object);

        // This test is only valid if the factory created the object if Autoloader did not pick it up automatically
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult) {
            $content = $this->_clearDocBlock(
                file_get_contents($this->_ioObject->getResultFileName(self::CLASS_NAME_WITH_NAMESPACE . 'Factory'))
            );
            $expectedContent = $this->_clearDocBlock(
                file_get_contents(__DIR__ . '/GeneratorTest/SourceClassWithNamespaceFactory.php')
            );
            $this->assertEquals($expectedContent, $content);
        }
    }

    public function testGenerateClassProxyWithoutNamespace()
    {
        $proxyClassName = self::CLASS_NAME_WITHOUT_NAMESPACE . 'Proxy';
        $result = false;
        $generatorResult = $this->_generator->generateClass($proxyClassName);
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult
            || \Magento\Code\Generator::GENERATION_SKIP == $generatorResult
        ) {
            $result = true;
        }
        $this->assertTrue($result);

        $proxy = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($proxyClassName);
        $this->assertInstanceOf(self::CLASS_NAME_WITHOUT_NAMESPACE, $proxy);

        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult) {
            $content = $this->_clearDocBlock(
                file_get_contents($this->_ioObject->getResultFileName(self::CLASS_NAME_WITHOUT_NAMESPACE . 'Proxy'))
            );
            $expectedContent = $this->_clearDocBlock(
                file_get_contents(
                    __DIR__ . '/GeneratorTest/SourceClassWithoutNamespaceProxy.php'
                )
            );
            $this->assertEquals($expectedContent, $content);
        }
    }

    public function testGenerateClassProxyWithNamespace()
    {
        $proxyClassName = self::CLASS_NAME_WITH_NAMESPACE . 'Proxy';
        $result = false;
        $generatorResult = $this->_generator->generateClass($proxyClassName);
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult
            || \Magento\Code\Generator::GENERATION_SKIP == $generatorResult
        ) {
            $result = true;
        }
        $this->assertTrue($result);

        $proxy = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create($proxyClassName);
        $this->assertInstanceOf(self::CLASS_NAME_WITH_NAMESPACE, $proxy);

        // This test is only valid if the factory created the object if Autoloader did not pick it up automatically
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult) {
            $content = $this->_clearDocBlock(
                file_get_contents($this->_ioObject->getResultFileName(self::CLASS_NAME_WITH_NAMESPACE . 'Proxy'))
            );
            $expectedContent = $this->_clearDocBlock(
                file_get_contents(__DIR__ . '/GeneratorTest/SourceClassWithNamespaceProxy.php')
            );
            $this->assertEquals($expectedContent, $content);
        }
    }

    public function testGenerateClassInterceptorWithoutNamespace()
    {
        $interceptorClassName = self::CLASS_NAME_WITHOUT_NAMESPACE . 'Interceptor';
        $interceptorClassName = self::CLASS_NAME_WITH_NAMESPACE . 'Interceptor';
        $result = false;
        $generatorResult = $this->_generator->generateClass($interceptorClassName);
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult
            || \Magento\Code\Generator::GENERATION_SKIP == $generatorResult
        ) {
            $result = true;
        }
        $this->assertTrue($result);

        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult) {
            $content = $this->_clearDocBlock(
                file_get_contents($this->_ioObject->
                        getResultFileName(self::CLASS_NAME_WITHOUT_NAMESPACE . 'Interceptor'))
            );
            $expectedContent = $this->_clearDocBlock(
                file_get_contents(
                    __DIR__ . '/GeneratorTest/SourceClassWithoutNamespaceInterceptor.php'
                )
            );
            $this->assertEquals($expectedContent, $content);
        }
    }

    public function testGenerateClassInterceptorWithNamespace()
    {
        $interceptorClassName = self::CLASS_NAME_WITH_NAMESPACE . 'Interceptor';
        $result = false;
        $generatorResult = $this->_generator->generateClass($interceptorClassName);
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult
            || \Magento\Code\Generator::GENERATION_SKIP == $generatorResult
        ) {
            $result = true;
        }
        $this->assertTrue($result);

        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult) {
            $content = $this->_clearDocBlock(
                file_get_contents($this->_ioObject->getResultFileName(self::CLASS_NAME_WITH_NAMESPACE . 'Interceptor'))
            );
            $expectedContent = $this->_clearDocBlock(
                file_get_contents(__DIR__ . '/GeneratorTest/SourceClassWithNamespaceInterceptor.php')
            );
            $this->assertEquals($expectedContent, $content);
        }
    }

    public function testGenerateInterfaceInterceptorWithoutNamespace()
    {
        $interceptorName = self::INTERFACE_NAME_WITHOUT_NAMESPACE . 'Interceptor';
        $result = false;
        $generatorResult = $this->_generator->generateClass($interceptorName);
        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult
            || \Magento\Code\Generator::GENERATION_SKIP == $generatorResult
        ) {
            $result = true;
        }
        $this->assertTrue($result);

        if (\Magento\Code\Generator::GENERATION_SUCCESS == $generatorResult) {
            $content = $this->_clearDocBlock(
                file_get_contents(
                    $this->_ioObject->getResultFileName(self::INTERFACE_NAME_WITHOUT_NAMESPACE . 'Interceptor')
                )
            );
            $expectedContent = $this->_clearDocBlock(
                file_get_contents(
                    __DIR__ . '/GeneratorTest/SourceInterfaceWithoutNamespaceInterceptor.php'
                )
            );
            $this->assertEquals($expectedContent, $content);
        }
    }
}
