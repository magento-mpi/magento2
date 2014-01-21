<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Block\Address\Renderer;

/**
 * DefaultRenderer
 */
class DefaultRendererTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Magento\Customer\Model\Address\Config
     */
    protected $_addressConfig;

    public function setUp()
    {
        /** @var  $addressConfig */
        $this->addressConfig = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Customer\Model\Address\Config');
    }

    /**
     * @dataProvider renderDataProvider
     */
    public function testRender($addressAttributes, $format, $expected)
    {
        /** @var \Magento\Customer\Block\Address\DefaultRenderer */
        $renderer = $this->addressConfig->getFormatByCode($format)->getRenderer();
        $actual = $renderer->render($addressAttributes);
        $this->assertEquals($expected, $actual);
    }

    public function renderDataProvider()
    {
        $addressAttributes = [
            'city' => 'CityM',
            'country_id' => 'US',
            'firstname' => 'John',
            'lastname' => 'Smith',
            'postcode' => '75477',
            'region' => 'Alabama',
            'region_id' => '1',
            'street' => ['Green str, 67'],
            'telephone' => '3468676',
        ];

        return [
            [
                $addressAttributes,
                \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML,
                "John Smith<br/>\n\nGreen str, 67<br />\n\n\n\nCityM,  Alabama, 75477<br/>\n<br/>\nT: 3468676\n\n"
            ],
            [
                $addressAttributes,
                \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_PDF,
                "John Smith|\n\nGreen str, 67\n\n\n\n\nCityM,|\nAlabama, 75477|\n|\nT: 3468676|\n|\n|"
            ],
            [
                $addressAttributes,
                \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_ONELINE,
                "John Smith, Green str, 67, CityM, Alabama 75477, "
            ],
            [
                $addressAttributes,
                \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_TEXT,
                "John Smith\n\nGreen str, 67\n\n\n\n\nCityM,  Alabama, 75477\n\nT: 3468676\n\n"
            ],
        ];
    }
}
