<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */

namespace Magento\Rule\Model\Condition;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rule\Model\Condition\Combine
     */
    protected $_combine;

    /**
     * @var ObjectManagerHelper
     */
    protected $_objectManagerHelper;

    /**
     * @var \Magento\Rule\Model\Condition\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_contextMock;

    protected function setUp()
    {
        $this->_objectManagerHelper = new ObjectManagerHelper($this);
        $this->_combine = $this->_objectManagerHelper->getObject('Magento\Rule\Model\Condition\Combine');
    }

    /**
     * @covers \Magento\Rule\Model\Condition\AbstractCondition::getValueName
     * @dataProvider optionValuesData
     * @param string|array $value
     * @param string $expectingData
     */
    public function testGetValueName($value, $expectingData)
    {
        $this->_combine->setValueOption(array('option_key' => 'option_value'))->setValue($value);
        $this->assertEquals($expectingData, $this->_combine->getValueName());
    }

    public function optionValuesData()
    {
        return array(
            array('option_key', 'option_value'),
            array('option_value', 'option_value'),
            array(array('option_key'), 'option_value'),
            array('', '...'),
        );
    }

}
