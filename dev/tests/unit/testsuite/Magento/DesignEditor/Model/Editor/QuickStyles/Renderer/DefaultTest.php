<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default renderer test
 */
class Magento_DesignEditor_Model_Editor_QuickStyles_Renderer_DefaultTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @cover Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Default::toCss
     * @dataProvider colorPickerData
     */
    public function testToCss($expectedResult, $data)
    {
        $rendererModel = $this->getMock(
            'Magento_DesignEditor_Model_Editor_Tools_QuickStyles_Renderer_Default', null, array(), '', false
        );

        $this->assertEquals($expectedResult, $rendererModel->toCss($data));
    }

    public function colorPickerData()
    {
        return array(array(
            'expected_result' => ".menu { color: red; }",
            'data'            => array(
                'type'      => 'color-picker',
                'default'   => '#f8f8f8',
                'selector'  => '.menu',
                'attribute' => 'color',
                'value'     => 'red',
            ),
        ));
    }
}
