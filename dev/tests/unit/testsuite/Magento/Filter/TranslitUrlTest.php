<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Filter;

class TranslitUrlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Filter\TranslitUrl
     */
    protected $model;

    protected function setUp()
    {
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject('Magento\Filter\TranslitUrl');
    }

    /**
     * @param string $testString
     * @param string $result
     * @param string $resultIconv
     * @param bool $isIconv
     * @dataProvider filterDataProvider
     */
    public function testFilter($testString, $result, $resultIconv, $isIconv)
    {
        if ($isIconv) {
            $this->assertEquals($resultIconv, $this->model->filter($testString));
        } else {
            $this->assertEquals($result, $this->model->filter($testString));
        }
    }

    /**
     * @return array
     */
    public function filterDataProvider()
    {
        $isIconv = '"libiconv"' == ICONV_IMPL;
        return array(
            array('test', 'test', 'test', $isIconv),
            array('привет мир', 'privet-mir', 'privet-mir', $isIconv),
            array(
                'Weiß, Goldmann, Göbel, Weiss, Göthe, Goethe und Götz',
                'weiss-goldmann-gobel-weiss-gothe-goethe-und-gotz',
                'weiss-goldmann-gobel-weiss-gothe-goethe-und-gotz',
                $isIconv
            ),
            array(
                '❤ ☀ ☆ ☂ ☻ ♞ ☯ ☭ ☢ € → ☎ ❄ ♫ ✂ ▷ ✇ ♎ ⇧ ☮',
                '❤ ☀ ☆ ☂ ☻ ♞ ☯ ☭ ☢ € → ☎ ❄ ♫ ✂ ▷ ✇ ♎ ⇧ ☮',
                'eur',
                $isIconv
            ),
            array(
                '™',
                'tm',
                'tm',
                $isIconv
            )
        );
    }
}
