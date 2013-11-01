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
     * @var \Magento\Code\Validator\ConstructorIntegrity
     */
    protected $_validator;

    /**
     * @var \Magento\ObjectManager\Config\Mapper\Dom()
     */
    protected $_mapper;

    protected function setUp()
    {
        $this->_shell = new \Magento\Shell();
        $basePath = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        $basePath = str_replace(DIRECTORY_SEPARATOR, '/', $basePath);
        $this->_tmpDir = realpath(__DIR__) . '/tmp';
        $this->_generationDir =  $this->_tmpDir . '/generation';
        $this->_compilationDir = $this->_tmpDir . '/di';
        $this->_command = 'php ' . $basePath
            . '/dev/tools/Magento/Tools/Di/compiler.php --generation=%s --di=%s';
        $this->_mapper = new \Magento\ObjectManager\Config\Mapper\Dom();
        $this->_validator = new \Magento\Code\Validator\ConstructorIntegrity();

        \Magento\Autoload\IncludePath::addIncludePath(array(
            $basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code',
            $basePath . DIRECTORY_SEPARATOR . 'lib',
            $basePath . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'generation',
        ));
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
                if (!isset($parameters['parameters']) || empty($parameters['parameters'])) {
                    continue;
                }

                if (\Magento\TestFramework\Utility\Classes::isVirtual($instanceName)) {
                    $instanceName = \Magento\TestFramework\Utility\Classes::resolveVirtualType($instanceName);
                }
                $parameters = $parameters['parameters'];

                if (!class_exists($instanceName)) {
                    $errors[] = 'Detected configuration of non existed class: ' . $instanceName . ' in file ' . $file;
                    continue;
                }

                $reflectionClass = new \ReflectionClass($instanceName);

                $constructor = $reflectionClass->getConstructor();
                if (!$constructor) {
                    $errors[] = 'Class ' . $instanceName . ' does not have __constructor';
                    continue;
                }

                $classParameters = $constructor->getParameters();
                foreach ($classParameters as $classParameter) {
                    $parameterName = $classParameter->getName();
                    if (array_key_exists($parameterName, $parameters)) {
                        unset($parameters[$parameterName]);
                    }
                }
                if (!empty($parameters)) {
                    $errors[] = 'Configuration of ' . $instanceName
                        . ' contains data for non-existed parameters: ' . implode(', ', array_keys($parameters))
                        . ' in file: ' . $file;
                }

            }
        }

        $failMessage = implode(PHP_EOL . PHP_EOL, $errors);
        $this->assertEmpty($errors, $failMessage);

        return empty($errors);

    }

    public function testConstructorIntegrity()
    {
        $this->markTestSkipped('Temporary disabled. Should be refactored due to 8 Nov 2013 by Folks team.');
        $basePath = \Magento\TestFramework\Utility\Files::init()->getPathToSource();

        $basePath = str_replace('/', '\\', $basePath);
        $libPath = $basePath . '\\lib';
        $appPath = $basePath . '\\app\\code';
        $generationPathPath = $basePath . '\\var\\generation';

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
        foreach ($classes as $className) {
            try {
                $this->_validator->validate($className);
            } catch (\Magento\Code\ValidationException $exceptions) {
                $errors[] = PHP_EOL . $exceptions->getMessage();
            }
        }
        $failMessage = implode(PHP_EOL, $errors);
        $this->assertEmpty($errors, $failMessage);

        return empty($errors);
    }

    /**
     * @depends testConfigurationOfInstanceParameters
     */
    public function testCompiler()
    {
        try {
            $this->_shell->execute(
                $this->_command,
                array($this->_generationDir, $this->_compilationDir)
            );
            $filesystem = new \Magento\Filesystem\Adapter\Local();
            $filesystem->delete($this->_tmpDir);
        } catch (\Magento\Exception $exception) {
            $filesystem = new \Magento\Filesystem\Adapter\Local();
            $filesystem->delete($this->_tmpDir);
            $this->fail($exception->getPrevious()->getMessage());
        }
    }
}
