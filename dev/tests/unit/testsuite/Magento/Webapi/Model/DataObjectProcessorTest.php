<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model;

use Magento\Webapi\Model\Config as ModelConfig;

class DataObjectProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var ModelConfig
     */
    protected $config;

    protected function setup()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Zend\Code\Reflection\ClassReflection $classRefection */
        $classRefection = new \Zend\Code\Reflection\ClassReflection('Magento\Webapi\Model\Files\TestDataInterface');
        $methodReflections = $classRefection->getMethods();

        $this->config = $this->getMockBuilder('Magento\Webapi\Model\Config')
            ->disableOriginalConstructor()
            ->setMethods(['getDataInterfaceMethods'])
            ->getMock();
        $this->config->expects($this->any())
            ->method('getDataInterfaceMethods')
            ->will($this->returnValue($methodReflections));
        $this->dataObjectProcessor = $objectManager->getObject(
            'Magento\Webapi\Model\DataObjectProcessor',
            ['config' => $this->config]
        );
        parent::setUp();
    }

    public function testDataObjectProcessor()
    {
        $objectManager =  new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Webapi\Model\Files\TestDataObject $testDataObject */
        $testDataObject = $objectManager->getObject('Magento\Webapi\Model\Files\TestDataObject');

        $expectedOutputDataArray = [
            'id' => '1',
            'address' => 'someAddress',
            'default_shipping' => 'true',
            'required_billing' => 'false'
        ];

        $testDataObjectType = 'Magento\Webapi\Model\Files\TestDataInterface';
        $outputData = $this->dataObjectProcessor->buildOutputDataArray($testDataObject, $testDataObjectType);
        $this->assertEquals($expectedOutputDataArray, $outputData);
    }
}
