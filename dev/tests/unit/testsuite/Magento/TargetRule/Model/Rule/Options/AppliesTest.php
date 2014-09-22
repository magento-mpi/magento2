<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Rule\Options;

use Magento\TestFramework\Helper\ObjectManager;

class AppliesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Applies
     */
    protected $_applies;

    public function setUp()
    {
        $rule = $this->getMock('\Magento\TargetRule\Model\Rule',
            [],
            [],
            '',
            false
        );

        $rule->expects($this->once())
            ->method('getAppliesToOptions')
            ->will($this->returnValue(array(1, 2)));

        $this->_applies = (new ObjectManager($this))->getObject(
            '\Magento\TargetRule\Model\Rule\Options\Applies',
            [
                'targetRuleModel' => $rule,
            ]
        );
    }

    public function testToOptionArray()
    {
        $this->assertEquals(array(1, 2), $this->_applies->toOptionArray());
    }
}
