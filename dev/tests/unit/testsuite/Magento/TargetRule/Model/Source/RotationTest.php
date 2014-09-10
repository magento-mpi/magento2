<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Source;

use Magento\TestFramework\Helper\ObjectManager;

class RotationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Rotation
     */
    protected $_rotation;

    public function setUp()
    {
        $this->_rotation = (new ObjectManager($this))->getObject('\Magento\TargetRule\Model\Source\Rotation', []);
    }

    public function testToOptionArray()
    {
        $result = array(
            \Magento\TargetRule\Model\Rule::ROTATION_NONE => __('Do not rotate'),
            \Magento\TargetRule\Model\Rule::ROTATION_SHUFFLE => __('Shuffle')
        );
        $this->assertEquals($result, $this->_rotation->toOptionArray());
    }
}
