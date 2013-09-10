<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Directory
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Directory_Model_Config_Source_CountryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Directory_Model_Config_Source_Country
     */
    protected $_model;

    /**
     * @var Magento_Directory_Model_Resource_Country_Collection
     */
    protected $_collectionMock;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_collectionMock = $this->getMock(
            'Magento_Directory_Model_Resource_Country_Collection', array(), array(), '', false
        );
        $arguments = array('countryCollection' => $this->_collectionMock);
        $this->_model = $objectManagerHelper->getObject('Magento_Directory_Model_Config_Source_Country', $arguments);

    }

    /**
     * @dataProvider toOptionArrayDataProvider
     * @param boolean $isMultiselect
     * @param string|array $foregroundCountries
     * @param array $expectedResult
     */
    public function testToOptionArray($isMultiselect, $foregroundCountries, $expectedResult)
    {
        $this->_collectionMock->expects($this->once())->method('loadData')->will($this->returnSelf());
        $this->_collectionMock->expects($this->once())->method('setForegroundCountries')
            ->with($foregroundCountries)
            ->will($this->returnSelf());
        $this->_collectionMock->expects($this->once())->method('toOptionArray')->will($this->returnValue(array()));
        $this->assertEquals($this->_model->toOptionArray($isMultiselect, $foregroundCountries), $expectedResult);
    }

    /**
     * @return array
     */
    public function toOptionArrayDataProvider()
    {
        return array(
            array(true, 'US', array()),
            array(false, 'US', array(array('value' => '', 'label' => __('--Please Select--')))),
            array(true, '', array()),
            array(false, '', array(array('value' => '', 'label' => __('--Please Select--')))),
            array(true, array('US', 'CA'), array()),
            array(false, array('US', 'CA'), array(array('value' => '', 'label' => __('--Please Select--')))),
        );
    }
}
