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
 * Background image renderer test
 */
namespace Magento\DesignEditor\Model\Editor\QuickStyles\Renderer;

class BackgroundImageTest
    extends \PHPUnit_Framework_TestCase
{
    /**
     * @cover \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\BackgroundImage::toCss
     * @dataProvider backgroundImageData
     */
    public function testToCss($expectedResult, $data)
    {
        /** @var $rendererModel \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\BackgroundImage */
        $rendererModel = $this->getMock(
            'Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\BackgroundImage', null, array(), '', false
        );

        $this->assertEquals($expectedResult, $rendererModel->toCss($data));
    }

    /**
     * @cover \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\BackgroundImage::toCss
     * @dataProvider backgroundImageDataClearDefault
     */
    public function testToCssClearDefault($expectedResult, $data)
    {
        /** @var $rendererModel \Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\BackgroundImage */
        $rendererModel = $this->getMock(
            'Magento\DesignEditor\Model\Editor\Tools\QuickStyles\Renderer\BackgroundImage', null, array(), '', false
        );

        $this->assertEquals($expectedResult, $rendererModel->toCss($data));
    }

    /**
     * @return array
     */
    public function backgroundImageData()
    {
        return array(array(
            'expected_result' => ".header { background-image: url('path/image.gif'); }",
            'data'            => array(
                'type'      => 'image-uploader',
                'default'   => 'bg.gif',
                'selector'  => '.header',
                'attribute' => 'background-image',
                'value'     => 'path/image.gif',
            ),
        ));
    }

    /**
     * @return array
     */
    public function backgroundImageDataClearDefault()
    {
        return array(array(
            'expected_result' => ".header { background-image: none; }",
            'data'            => array(
                'type'      => 'image-uploader',
                'default'   => 'bg.gif',
                'selector'  => '.header',
                'attribute' => 'background-image',
                'value'     => '',
            ),
        ));
    }
}
