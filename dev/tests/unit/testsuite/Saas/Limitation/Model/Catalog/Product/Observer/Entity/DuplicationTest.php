<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer_Entity_DuplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Catalog_Product_Observer_Entity_Duplication
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_limitationValidator;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_limitation;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_dictionary;

    protected function setUp()
    {
        $this->_limitationValidator = $this->getMock(
            'Saas_Limitation_Model_Limitation_Validator', array('exceedsThreshold'), array(), '', false
        );
        $this->_limitation = $this->getMockForAbstractClass('Saas_Limitation_Model_Limitation_LimitationInterface');
        $this->_dictionary = $this->getMock(
            'Saas_Limitation_Model_Dictionary', array('getMessage'), array(), '', false
        );
        $this->_dictionary
            ->expects($this->once())
            ->method('getMessage')
            ->with('fixture_message')
            ->will($this->returnValue('Fixture Message Text'))
        ;
        $this->_model = new Saas_Limitation_Model_Catalog_Product_Observer_Entity_Duplication(
            $this->_limitationValidator,
            $this->_limitation,
            $this->_dictionary,
            'fixture_message'
        );
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Fixture Message Text
     */
    public function testRestrictActive()
    {
        $this->_limitationValidator
            ->expects($this->once())
            ->method('exceedsThreshold')
            ->with($this->_limitation)
            ->will($this->returnValue(true))
        ;
        $this->_model->restrict(new Varien_Event_Observer);
    }

    public function testRestrictNewEntityCreationInactive()
    {
        $this->_limitationValidator
            ->expects($this->once())
            ->method('exceedsThreshold')
            ->with($this->_limitation)
            ->will($this->returnValue(false))
        ;
        $this->_model->restrict(new Varien_Event_Observer);
    }
}
