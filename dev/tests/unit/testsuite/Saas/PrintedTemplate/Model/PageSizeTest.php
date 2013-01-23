<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_PrintedTemplate_Model_PageSizeTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test Saas_PrintedTemplate_Model_PageSize::__construct
     *
     * @param array $sizeInfo
     * @param array $expectedData
     *
     * @dataProvider testConstructorProvider
     */
    public function testConstructor($sizeInfo, $expectedSize, $measureInstance)
    {
        $config = array('name' => $sizeInfo['name']);
        $unit = isset($sizeInfo['unit'])
            ? strtoupper($sizeInfo['unit'])
            : Zend_Measure_Length::MILLIMETER;
        if (isset($sizeInfo['width'])) {
            $config['width'] = new Zend_Measure_Length($sizeInfo['width'], $unit, $sizeInfo['locale']);
        }
        if (isset($sizeInfo['height'])) {
            $config['height'] = new Zend_Measure_Length($sizeInfo['width'], $unit, $sizeInfo['locale']);
        }

        $pageSize = new Saas_PrintedTemplate_Model_PageSize($config);

        $this->assertEquals($expectedSize, $pageSize->getData('width'));
        if ($measureInstance) {
            $this->assertInstanceOf($measureInstance, $pageSize->getData('height'));
        }
    }

    /**
     * Provider for testConstructor
     *
     * @return array
     */
    public function testConstructorProvider()
    {
        return array(
            array(
                array(
                    'name' => 'a4',
                    'unit' => 'MILLIMETER',
                    'width' => '50',
                    'height' => '50',
                    'locale' => 'en_US'
                ),
                '50 mm',
                'Zend_Measure_Length'
            ),
            array(
                array(
                    'name' => 'letter',
                    'locale' => 'fr_FR'
                ),
                null,
                null
            ),
            array(
                array(
                    'name' => 'letter',
                    'unit' => 'inch',
                    'width' => '30',
                    'height' => '30',
                    'locale' => 'zh_CN'
                ),
                '30 in',
                'Zend_Measure_Length'
            )
        );
    }
}
