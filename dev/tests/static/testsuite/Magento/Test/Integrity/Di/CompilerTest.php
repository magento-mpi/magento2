<?php
/**
 * Compiler test. Check compilation of DI definitions and code generation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Di;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CompilerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_command;

    /**
     * @var \Magento\Shell
     */
    protected $_shell;

    /**
     * @var string
     */
    protected $_generationDir;

    /**
     * @var string
     */
    protected $_compilationDir;

    /**
     * @var string
     */
    protected $_tmpDir;

    /**
     * @var \Magento\ObjectManager\Config\Mapper\Dom()
     */
    protected $_mapper;

    /**
     * @var \Magento\Code\Validator\ConstructorIntegrity
     */
    protected $_validator;

    protected function setUp()
    {
        $this->_shell = new \Magento\Shell();
        $basePath = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $basePath = str_replace(DIRECTORY_SEPARATOR, '/', $basePath);

        $this->_tmpDir = realpath(__DIR__) . '/tmp';
        $this->_generationDir =  $this->_tmpDir . '/generation';
        $this->_compilationDir = $this->_tmpDir . '/di';

        \Magento\Autoload\IncludePath::addIncludePath(array(
            $basePath . '/app/code',
            $basePath . '/lib',
            $this->_generationDir,
        ));

        $this->_command = 'php ' . $basePath
            . '/dev/tools/Magento/Tools/Di/compiler.php --generation=%s --di=%s';
        $this->_mapper = new \Magento\ObjectManager\Config\Mapper\Dom();
        $this->_validator = new \Magento\Code\Validator\ConstructorIntegrity();
    }

    protected function tearDown()
    {
        $filesystem = new \Magento\Filesystem\Adapter\Local();
        $filesystem->delete($this->_tmpDir);
    }

    /**
     * Perform class(instance) and its parameters analysis
     * return error message if found issues
     *
     * @param $file
     * @param $instanceName
     * @param $parameters
     * @return string|null
     */
    protected function analyzeInstance($file, $instanceName, $parameters)
    {
        if (!isset($parameters['parameters']) || empty($parameters['parameters'])) {
            return null;
        }

        if (\Magento\TestFramework\Utility\Classes::isVirtual($instanceName)) {
            $instanceName = \Magento\TestFramework\Utility\Classes::resolveVirtualType($instanceName);
        }
        $parameters = $parameters['parameters'];

        if (!class_exists($instanceName)) {
            return 'Detected configuration of non existed class: ' . $instanceName . ' in file ' . $file;
        }

        $reflectionClass = new \ReflectionClass($instanceName);

        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) {
            return 'Class ' . $instanceName . ' does not have __constructor';
        }

        $classParameters = $constructor->getParameters();
        foreach ($classParameters as $classParameter) {
            $parameterName = $classParameter->getName();
            if (array_key_exists($parameterName, $parameters)) {
                unset($parameters[$parameterName]);
            }
        }

        if (!empty($parameters)) {
            return 'Configuration of ' . $instanceName
                . ' contains data for non-existed parameters: ' . implode(', ', array_keys($parameters))
                . ' in file: ' . $file;
        }
    }

    public function testConfigurationOfInstanceParameters()
    {
        $files = \Magento\TestFramework\Utility\Files::init()->getDiConfigs();
        $errors = array();
        foreach ($files as $file) {
            $dom = new \DOMDocument();
            $dom->load($file);
            $data = $this->_mapper->convert($dom);

            foreach ($data as $instanceName => $parameters) {
                $message = $this->analyzeInstance($file, $instanceName, $parameters);
                if (!empty($message)) {
                    $errors[] = $message;
                }
            }
        }

        $failMessage = implode(PHP_EOL . PHP_EOL, $errors);
        $this->assertEmpty($errors, $failMessage);

        return empty($errors);
    }

    public function testConstructorIntegrity()
    {
        $autoloader = new \Magento\Autoload\IncludePath();
        $generatorIo = new \Magento\Code\Generator\Io(new \Magento\Io\File(), $autoloader, $this->_generationDir);
        $generator = new \Magento\Code\Generator(null, $autoloader, $generatorIo);
        $autoloader = new \Magento\Code\Generator\Autoloader($generator);
        spl_autoload_register(array($autoloader, 'load'));

        $basePath = \Magento\TestFramework\Utility\Files::init()->getPathToSource();

        $basePath = str_replace('/', '\\', $basePath);
        $libPath = $basePath . '\\lib';
        $appPath = $basePath . '\\app\\code';
        $generationPathPath = str_replace('/', '\\', $this->_generationDir);

        $files = \Magento\TestFramework\Utility\Files::init()->getClassFiles(
            true, false, false, false, false, true, false
        );

        $patterns  = array(
            '/' . preg_quote($libPath) . '/',
            '/' . preg_quote($appPath) . '/',
            '/' . preg_quote($generationPathPath) . '/'
        );
        $replacements  = array('', '', '');

        $classes = array();
        foreach ($files as $file) {
            $file = str_replace('/', '\\', $file);
            $filePath = preg_replace($patterns, $replacements, $file);
            $className = substr($filePath, 0, -4);
            if (class_exists($className)) {
                $classes[$file] = $className;
            }
        }

        $errors = array();
        foreach ($classes as $file => $className) {
            try {
                $this->_validator->validate($className);
            } catch (\Magento\Code\ValidationException $exceptions) {
                $errors[] = PHP_EOL . $exceptions->getMessage();
            } catch (\ReflectionException $exceptions) {
                $errors[] = PHP_EOL . $exceptions->getMessage();
            }
        }

        spl_autoload_unregister(array($autoloader, 'load'));

        $failMessage = implode(PHP_EOL, $errors);
        $this->assertEmpty($errors, $failMessage);

        return empty($errors);
    }

    /**
     * @depends testConfigurationOfInstanceParameters
     * @depends testConstructorIntegrity
     */
    public function testCompiler()
    {
        try {
            $this->_shell->execute(
                $this->_command,
                array($this->_generationDir, $this->_compilationDir)
            );
        } catch (\Magento\Exception $exception) {
            $this->fail($exception->getPrevious()->getMessage());
        }
    }
}
