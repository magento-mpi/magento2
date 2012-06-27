<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Block_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $expectedResult
     * @param string $nameInLayout
     * @param array $methodArguments
     * @dataProvider dataGetUiId
     */
    public function testGetUiId($expectedResult, $nameInLayout, $methodArguments)
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $this->getMock('Mage_Core_Block_Abstract', null);
        $block->setNameInLayout($nameInLayout);

        $this->assertEquals(
            $expectedResult,
            call_user_func_array(array($block, 'getUiId'), $methodArguments)
        );
    }

    public static function dataGetUiId()
    {
        return array(
            array(' data-ui-id="" ', null, array()),
            array(' data-ui-id="block" ', 'block', array()),
            array(' data-ui-id="block" ', 'block---', array()),
            array(' data-ui-id="block" ', '--block', array()),
            array(' data-ui-id="bl-ock" ', '--bl--ock---', array()),
            array(' data-ui-id="bl-ock" ', '--bL--Ock---', array()),
            array(' data-ui-id="b-l-o-c-k" ', '--b!@#$%^&**()L--O;:...c<_>k---', array()),
            array(' data-ui-id="a0b1c2d3e4f5g6h7-i8-j9k0l1m2n-3o4p5q6r7-s8t9u0v1w2z3y4x5" ',
                'a0b1c2d3e4f5g6h7', array('i8-j9k0l1m2n-3o4p5q6r7', 's8t9u0v1w2z3y4x5')
            ),
            array(' data-ui-id="capsed-block-name-cap-ed-param1-caps2-but-ton" ',
                'CaPSed BLOCK NAME', array('cAp$Ed PaRaM1', 'caPs2', 'bUT-TOn')
            ),
            array(' data-ui-id="block-0-1-2-3-4-5-6-7-8-9-10-11-12-13-14-15-16-17-18-19-20" ',
                '!block!', range(0, 20)
            ),
        );
    }

    public function testSetNameInLayout()
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $this->getMock('Mage_Core_Block_Abstract', array('getLayout'));

        $layout = $this->getMock('Mage_Core_Model_Layout', array('renameElement'), array(), '', false);
        $layout->expects($this->once())
            ->method('renameElement')
            ->with('name', 'another-name');

        $block->expects($this->exactly(5))
            ->method('getLayout')
            ->with()
            ->will($this->onConsecutiveCalls($layout, $layout, $layout, $layout, null));

        $this->assertEquals(null, PHPUnit_Framework_Assert::readAttribute($block, '_layout'));
        $this->assertEquals(null, PHPUnit_Framework_Assert::readAttribute($block, '_nameInLayout'));

        // block name is empty
        $block->setNameInLayout(null);

        $this->assertEquals(null, PHPUnit_Framework_Assert::readAttribute($block, '_layout'));
        $this->assertEquals(null, PHPUnit_Framework_Assert::readAttribute($block, '_nameInLayout'));

        // block name become non-empty
        $block->setNameInLayout('name');

        $this->assertEquals(null, PHPUnit_Framework_Assert::readAttribute($block, '_layout'));
        $this->assertEquals('name', PHPUnit_Framework_Assert::readAttribute($block, '_nameInLayout'));

        // block name set to the same value
        $block->setNameInLayout('name');

        $this->assertEquals(null, PHPUnit_Framework_Assert::readAttribute($block, '_layout'));
        $this->assertEquals('name', PHPUnit_Framework_Assert::readAttribute($block, '_nameInLayout'));

        // block name set to another value
        $block->setNameInLayout('another-name');

        $this->assertEquals(null, PHPUnit_Framework_Assert::readAttribute($block, '_layout'));
        $this->assertEquals('another-name', PHPUnit_Framework_Assert::readAttribute($block, '_nameInLayout'));

        // block name set to one more value, but layout is empty
        $block->setNameInLayout('one-more-name');

        $this->assertEquals(null, PHPUnit_Framework_Assert::readAttribute($block, '_layout'));
        $this->assertEquals('one-more-name', PHPUnit_Framework_Assert::readAttribute($block, '_nameInLayout'));
    }
}
