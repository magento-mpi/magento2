<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer_Entity_VariationsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Catalog_Product_Observer_Entity_Variations
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
            ->will($this->returnValue('You tried to add %d products, but the most you can have is %d'))
        ;
        $this->_model = new Saas_Limitation_Model_Catalog_Product_Observer_Entity_Variations(
            $this->_limitationValidator,
            $this->_limitation,
            $this->_dictionary,
            'fixture_message'
        );
    }

    /**
     * Invoke creation restriction
     *
     * @param int $threshold
     * @param bool $isThresholdReached
     * @param bool $isObjectNew
     * @param array $variations
     * @param int $expectedQty
     */
    protected function _invokeRestrictCreation(
        $threshold, $isThresholdReached, $isObjectNew, array $variations, $expectedQty
    ) {
        $this->_limitationValidator
            ->expects($this->any())
            ->method('exceedsThreshold')
            ->with($this->_limitation, $expectedQty)
            ->will($this->returnValue($isThresholdReached))
        ;
        $this->_limitation->expects($this->any())->method('getThreshold')->will($this->returnValue($threshold));
        $entity = $this->getMock('Mage_Core_Model_Abstract', array('isObjectNew'), array(), '', false);
        $entity->expects($this->any())->method('isObjectNew')->will($this->returnValue($isObjectNew));
        $this->_model->restrictCreation(new Varien_Event_Observer(array(
            'event' => new Varien_Object(array('product' => $entity, 'variations' => $variations))
        )));
    }

    /**
     * @param int $threshold
     * @param bool $isObjectNew
     * @param array $variations
     * @param int $expectedQty
     * @param string $expectedExceptionMsg
     * @dataProvider restrictCreationActiveDataProvider
     */
    public function testRestrictCreationActive(
        $threshold, $isObjectNew, array $variations, $expectedQty, $expectedExceptionMsg
    ) {
        $this->setExpectedException('Mage_Catalog_Exception', $expectedExceptionMsg);
        $this->_invokeRestrictCreation($threshold, true, $isObjectNew, $variations, $expectedQty);
    }

    public function restrictCreationActiveDataProvider()
    {
        return array(
            'existing object' => array(
                3, false, array('S', 'M', 'L', 'XL'), 4, 'You tried to add 4 products, but the most you can have is 3'
            ),
            'new object' => array(
                3, true, array('S', 'M', 'L', 'XL'), 5, 'You tried to add 5 products, but the most you can have is 3',
            ),
        );
    }

    /**
     * @param int $threshold
     * @param bool $isThresholdReached
     * @param bool $isObjectNew
     * @param array $variations
     * @param int $expectedQty
     * @dataProvider restrictCreationInactiveDataProvider
     */
    public function testRestrictCreationInactive(
        $threshold, $isThresholdReached, $isObjectNew, array $variations, $expectedQty
    ) {
        $this->_invokeRestrictCreation($threshold, $isThresholdReached, $isObjectNew, $variations, $expectedQty);
    }

    public function restrictCreationInactiveDataProvider()
    {
        return array(
            'threshold not reached & existing object' => array(
                4, false, false, array('S', 'M', 'L', 'XL'), 4
            ),
            'threshold not reached & new object' => array(
                5, false, true, array('S', 'M', 'L', 'XL'), 5
            ),
            'threshold reached & existing object & no variations' => array(
                3, true, false, array(), 0
            ),
        );
    }
}
