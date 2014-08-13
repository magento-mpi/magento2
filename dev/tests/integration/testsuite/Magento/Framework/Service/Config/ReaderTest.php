<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Config;

/**
 * Tests for \Magento\Framework\Service\Config\Reader
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Service\Config\Reader
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_fileList;

    /**
     * @var \Magento\Framework\App\Arguments\FileResolver\Primary
     */
    protected $_fileResolverMock;

    /**
     * @var \Magento\Framework\App\Arguments\ValidationState
     */
    protected $_validationState;

    /**
     * @var \Magento\Framework\Service\Config\SchemaLocator
     */
    protected $_schemaLocator;

    /**
     * @var \Magento\Framework\Service\Config\Converter
     */
    protected $_converter;

    protected function setUp()
    {
        $fixturePath = realpath(__DIR__ . '/_files') . '/';
        $this->_fileList = array(
            file_get_contents($fixturePath . 'config_one.xml'),
            file_get_contents($fixturePath . 'config_two.xml')
        );

        $this->_fileResolverMock = $this->getMockBuilder('Magento\Framework\App\Arguments\FileResolver\Primary')
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();
        $this->_fileResolverMock->expects($this->once())
            ->method('get')
            ->will($this->returnValue($this->_fileList));

        $this->_converter = new \Magento\Framework\Service\Config\Converter();

        $this->_validationState = new \Magento\Framework\App\Arguments\ValidationState(
            \Magento\Framework\App\State::MODE_DEFAULT
        );
        $this->_schemaLocator = new \Magento\Framework\Service\Config\SchemaLocator();
    }

    public function testMerge()
    {
        $model = new \Magento\Framework\Service\Config\Reader(
            $this->_fileResolverMock,
            $this->_converter,
            $this->_schemaLocator,
            $this->_validationState
        );

        $expectedArray = [
            'Magento\Tax\Service\V1\Data\TaxRate' => [],
            'Magento\Catalog\Service\Data\V1\Product' => [
                'stock_item' => "Magento\CatalogInventory\Service\Data\V1\StockItem"
            ],
            'Magento\Customer\Service\V1\Data\Customer' => [
                'custom_1' => "Magento\Customer\Service\V1\Data\CustomerCustom",
                'custom_2' => "Magento\CustomerExtra\Service\V1\Data\CustomerCustom22",
                'custom_3' => "Magento\Customer\Service\V1\Data\CustomerCustom3"
            ]
        ];

        $this->assertEquals($expectedArray, $model->read('global'));
    }
}
