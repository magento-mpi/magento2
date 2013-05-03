<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Directory
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Directory_Model_Config_Source_CountryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Directory_Model_Config_Source_Country
     */
    protected $_model;
    
    /**
     * @var Mage_Directory_Model_Resource_Country_Collection
     */
    protected $_collectionMock;

    protected function setUp()
    {
        $this->_collectionMock = $this->getMock(
            'Mage_Directory_Model_Resource_Country_Collection', array(), array(), '', false
         );
        $helperMock = $this->getMock('Mage_Directory_Helper_Data', array(), array(), '', false);
        $this->_model = new Mage_Directory_Model_Config_Source_Country($this->_collectionMock, $helperMock);
    }
    
    public function testToOptionArray()
    {
        $this->_collectionMock->expects($this->once())->method('loadData')->will($this->returnSelf());;
        $this->_collectionMock->expects($this->once())->method('setForegroundCountries')
            ->with('US')
            ->will($this->returnSelf());
        $this->_collectionMock->expects($this->once())->method('toOptionArray')->will($this->returnValue(array()));;
        $this->assertEquals($this->_model->toOptionArray(false, 'US'), array(array('value' => '', 'label' => '')));
    }
}

