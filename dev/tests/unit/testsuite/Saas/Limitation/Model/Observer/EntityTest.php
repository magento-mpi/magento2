<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Observer_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Observer_Entity
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
        $this->_model = new Saas_Limitation_Model_Observer_Entity(
            $this->_limitationValidator,
            $this->_limitation,
            $this->_dictionary,
            'fixture_message'
        );
    }

    /**
     * Invoke restriction enforcement
     *
     * @param bool $isThresholdReached
     * @param bool $isObjectNew
     */
    protected function _invokeRestrictCreation($isThresholdReached, $isObjectNew)
    {
        $this->_limitationValidator
            ->expects($this->any())
            ->method('exceedsThreshold')
            ->with($this->_limitation)
            ->will($this->returnValue($isThresholdReached))
        ;
        $model = $this->getMock('Mage_Core_Model_Abstract', array('isObjectNew'), array(), '', false);
        $model->expects($this->once())->method('isObjectNew')->will($this->returnValue($isObjectNew));
        $this->_model->restrictCreation(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('data_object' => $model))
        )));
    }

    /**
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Fixture Message Text
     */
    public function testRestrictCreationActive()
    {
        $this->_invokeRestrictCreation(true, true);
    }

    /**
     * Test that no restriction is applied until conditions are met
     *
     * @param bool $isThresholdReached
     * @param bool $isObjectNew
     * @dataProvider restrictCreationInactiveDataProvider
     */
    public function testRestrictCreationInactive($isThresholdReached, $isObjectNew)
    {
        $this->_invokeRestrictCreation($isThresholdReached, $isObjectNew);
    }

    public function restrictCreationInactiveDataProvider()
    {
        return array(
            'threshold not reached & existing object'   => array(false, false),
            'threshold not reached & new object'        => array(false, true),
            'threshold reached & existing object'       => array(true, false),
        );
    }
}
