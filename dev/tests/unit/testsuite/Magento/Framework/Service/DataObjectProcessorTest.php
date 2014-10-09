<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\Service;

class DataObjectProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    protected function setup()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->dataObjectProcessor = $objectManager->getObject('Magento\Framework\Service\DataObjectProcessor');
        parent::setUp();
    }

    public function testDataObjectProcessor()
    {
        $objectManager =  new \Magento\TestFramework\Helper\ObjectManager($this);
        /** @var \Magento\Framework\Service\Files\TestDataObject $testDataObject */
        $testDataObject = $objectManager->getObject('Magento\Framework\Service\Files\TestDataObject');

        $expectedOutputDataArray = [
            'id' => '1',
            'address' => 'someAddress',
            'default_shipping' => 'true',
            'required_billing' => 'false'
        ];

        $testDataObjectType = 'Magento\Framework\Service\Files\TestDataInterface';
        $outputData = $this->dataObjectProcessor->buildOutputDataArray($testDataObject, $testDataObjectType);
        $this->assertEquals($expectedOutputDataArray, $outputData);
    }
}
