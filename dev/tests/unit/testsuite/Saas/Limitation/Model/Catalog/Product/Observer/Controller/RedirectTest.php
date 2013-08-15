<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer_Controller_RedirectTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Catalog_Product_Observer_Controller_Redirect
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
        $this->_model = new Saas_Limitation_Model_Catalog_Product_Observer_Controller_Redirect(
            $this->_limitationValidator,
            $this->_limitation,
            $this->_dictionary,
            'fixture_message'
        );
    }

    /**
     * @expectedException Mage_Catalog_Exception
     * @expectedExceptionMessage Fixture Message Text
     */
    public function testRestrictNewEntityCreationActive()
    {
        $this->_limitationValidator
            ->expects($this->once())
            ->method('exceedsThreshold')
            ->with($this->_limitation)
            ->will($this->returnValue(true))
        ;

        $request = $this->getMock('Zend_Controller_Request_Abstract', array('getParam'), array(), '', false);
        $controller = $this->getMock(
            'Mage_Adminhtml_Controller_Catalog_Product', array('getRequest'), array(), '', false
        );
        $request->expects($this->once())->method('getParam')->with('back')->will($this->returnValue('new'));
        $controller->expects($this->once())->method('getRequest')->will($this->returnValue($request));

        $this->_model->restrictNewEntityCreation(new Magento_Event_Observer(array(
            'event' => new Magento_Object(array('controller' => $controller))
        )));
    }

    /**
     * @param bool $isThresholdReached
     * @param string $redirectTarget
     * @dataProvider restrictNewEntityCreationInactiveDataProvider
     */
    public function testRestrictNewEntityCreationInactive($isThresholdReached, $redirectTarget)
    {
        $this->_limitationValidator
            ->expects($this->any())
            ->method('exceedsThreshold')
            ->with($this->_limitation)
            ->will($this->returnValue($isThresholdReached))
        ;

        $request = $this->getMock('Zend_Controller_Request_Abstract', array('getParam'), array(), '', false);
        $controller = $this->getMock(
            'Mage_Adminhtml_Controller_Catalog_Product', array('getRequest'), array(), '', false
        );
        $request->expects($this->any())->method('getParam')->with('back')->will($this->returnValue($redirectTarget));
        $controller->expects($this->any())->method('getRequest')->will($this->returnValue($request));

        $this->_model->restrictNewEntityCreation(new Magento_Event_Observer(array(
            'event' => new Magento_Object(array('controller' => $controller))
        )));
    }

    public function restrictNewEntityCreationInactiveDataProvider()
    {
        return array(
            'limitation not reached & relevant redirect'    => array(false, 'new'),
            'limitation not reached & irrelevant redirect'  => array(false, 'irrelevant'),
            'limitation reached & irrelevant redirect'      => array(true, 'irrelevant'),
        );
    }
}
