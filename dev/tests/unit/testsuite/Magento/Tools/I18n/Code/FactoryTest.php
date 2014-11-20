<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\I18n\Code;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Tools\I18n\Code\Factory
     */
    protected $factory;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->factory = $objectManagerHelper->getObject('Magento\Tools\I18n\Code\Factory');
    }

    /**
     * @param string $expectedInstance
     * @param string $fileName
     * @dataProvider createDictionaryWriterDataProvider
     */
    public function testCreateDictionaryWriter($expectedInstance, $fileName)
    {
        $this->assertInstanceOf(
            $expectedInstance,
            $this->factory->createDictionaryWriter($fileName)
        );
    }

    /**
     * @return array
     */
    public function createDictionaryWriterDataProvider()
    {
        return [
            [
                'Magento\Tools\I18n\Code\Dictionary\Writer\Csv',
                'filename.invalid_type'
            ],
            [
                'Magento\Tools\I18n\Code\Dictionary\Writer\Csv',
                'filename'
            ],
            [
                'Magento\Tools\I18n\Code\Dictionary\Writer\Csv',
                'filename.csv'
            ],
            [
                'Magento\Tools\I18n\Code\Dictionary\Writer\Csv\Stdo',
                ''
            ],
        ];
    }
}
