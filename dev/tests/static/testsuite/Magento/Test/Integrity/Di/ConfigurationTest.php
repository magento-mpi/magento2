<?php
/**
 * DI configuration test. Checks configuration of types and virtual types parameters
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Di;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ObjectManager\Config\Mapper\Dom()
     */
    protected $_mapper;

    protected function setUp()
    {
        $basePath = \Magento\TestFramework\Utility\Files::init()->getPathToSource();
        \Magento\Autoload\IncludePath::addIncludePath(array(
            $basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code',
            $basePath . DIRECTORY_SEPARATOR . 'lib',
        ));
        $this->_mapper = new \Magento\ObjectManager\Config\Mapper\Dom();

    }

    /**
     * @dataProvider configFilesDataProvider
     */
    public function testConfigurationOfInstanceParameters($file)
    {
        $dom = new \DOMDocument();
        $dom->load($file);
        $data = $this->_mapper->convert($dom);

        foreach ($data as $instanceName => $parameters) {
            if (!isset($parameters['parameters'])
                || empty($parameters['parameters'])
            ) {
                continue;
            }

            if (\Magento\TestFramework\Utility\Classes::isVirtual($instanceName)) {
                $instanceName = \Magento\TestFramework\Utility\Classes::resolveVirtualType($instanceName);
            }
            $parameters = $parameters['parameters'];

            $path = \Magento\Autoload\IncludePath::getFilePath($instanceName);
            if (class_exists($instanceName)) {
                require_once $path;
            } else {
                $this->fail('Non existed class: ' . $instanceName);
            }

            $reflectionClass = new \ReflectionClass($instanceName);

            $constructor = $reflectionClass->getConstructor();
            if (!$constructor) {
                $this->fail('Class ' . $instanceName . ' does not have __constructor');
            }

            $classParameters = $constructor->getParameters();
            foreach ($classParameters as $classParameter) {
                $parameterName = $classParameter->getName();
                if (array_key_exists($parameterName, $parameters)) {
                    unset($parameters[$parameterName]);
                }
            }
            $this->assertEmpty($parameters,
                'Configuration of ' . $instanceName
                . ' contains data for non-existed parameters: ' . implode(', ', array_keys($parameters))
                . ' in file: ' . $file
            );
        }
    }

    /**
     * @return array
     */
    public function configFilesDataProvider()
    {
        $output = array();
        $files = \Magento\TestFramework\Utility\Files::init()->getDiConfigs();
        foreach ($files as $file) {
            $output[$file] = array($file);
        }
        return $output;
    }
}
