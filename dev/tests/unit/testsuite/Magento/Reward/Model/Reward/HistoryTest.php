<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Reward\Model\Reward;

class HistoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reward\Model\Reward\History
     */
    protected $_model;

    protected function setUp()
    {
        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Magento\Reward\Model\Reward\History');
    }

    public function testGetAdditionalDataEmpty()
    {
        $this->assertSame(array(), $this->_model->getAdditionalData());
    }

    public function testGetAdditionalDataNotEmpty()
    {
        $value = array('field1' => 'value1', 'field2' => 'value2');
        $this->_model->setData('additional_data', $value);
        $this->assertEquals($value, $this->_model->getAdditionalData());
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Additional data for a reward point history has to be an array
     */
    public function testGetAdditionalDataInvalid()
    {
        $this->_model->setData('additional_data', 'not an array');
        $this->_model->getAdditionalData();
    }

    /**
     * @param string $inputKey
     * @param string $expectedResult
     * @dataProvider getAdditionalDataByKeyDataProvider
     */
    public function testGetAdditionalDataByKey($inputKey, $expectedResult)
    {
        $this->_model->setData('additional_data', array('field' => 'value'));
        $this->assertSame($expectedResult, $this->_model->getAdditionalDataByKey($inputKey));
    }

    public function getAdditionalDataByKeyDataProvider()
    {
        return array(
            'existing field' => array('field', 'value'),
            'unknown field'  => array('unknown', null),
        );
    }

    /**
     * @param array $inputData
     * @param array $expectedResult
     * @dataProvider getAdditionalDataDataProvider
     */
    public function testAddAdditionalData(array $inputData, array $expectedResult)
    {
        $this->_model->setData('additional_data', array('field1' => 'value1', 'field2' => 'value2'));
        $this->_model->addAdditionalData($inputData);
        $this->assertEquals($expectedResult, $this->_model->getAdditionalData());
    }

    public function getAdditionalDataDataProvider()
    {
        return array(
            'adding new field' => array(
                array('field3' => 'value3'),
                array('field1' => 'value1', 'field2' => 'value2', 'field3' => 'value3'),
            ),
            'overriding existing field' => array(
                array('field1' => 'overridden_value'),
                array('field1' => 'overridden_value', 'field2' => 'value2'),
            ),
        );
    }
}
