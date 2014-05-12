<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default renderer test
 */
namespace Magento\DesignEditor\Model\Editor\QuickStyles\Renderer;

class DefaultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\DefaultRenderer::toCss
     * @dataProvider colorPickerData
     */
    public function testToCss($expectedResult, $data)
    {
        $rendererModel = $this->getMock(
            'Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\DefaultRenderer',
            null,
            array(),
            '',
            false
        );

        $this->assertEquals($expectedResult, $rendererModel->toCss($data));
    }

    public function colorPickerData()
    {
        return array(
            array(
                'expected_result' => ".menu { color: red; }",
                'data' => array(
                    'type' => 'color-picker',
                    'default' => '#f8f8f8',
                    'selector' => '.menu',
                    'attribute' => 'color',
                    'value' => 'red'
                )
            )
        );
    }
}
