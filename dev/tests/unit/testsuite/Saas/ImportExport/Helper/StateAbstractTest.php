<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_StateAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stateFlagMock;

    /**
     * @var Stub_Saas_ImportExport_Helper_StateAbstract
     */
    protected $_helperModel;

    public function setUp()
    {
        $this->_stateFlagMock = $this->getMock('Saas_ImportExport_Model_State_Flag', array(), array(), '', false);
        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_helperModel = $objectManager->getObject('Stub_Saas_ImportExport_Helper_StateAbstract', array(
            'context' => $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false),
            'stateFlag' => $this->_stateFlagMock,
            'dataHelper' => $this->getMock('Saas_ImportExport_Helper_Data', array(), array(), '', false)
        ));
    }

    /**
     * @return array
     */
    public function dataProviderForTrueFalseTest()
    {
        return array(
            array(true, true),
            array(false, false),
        );
    }

    /**
     * @param bool $returnValue
     * @param bool $expectedValue
     * @dataProvider dataProviderForTrueFalseTest
     */
    public function testIsInProgress($returnValue, $expectedValue)
    {
        $this->_stateFlagMock->expects($this->once())->method('isInProgress')->will($this->returnValue($returnValue));
        $this->assertEquals($expectedValue, $this->_helperModel->isInProgress());
    }

    /**
     * @param bool $returnValue
     * @param bool $expectedValue
     * @dataProvider dataProviderForTrueFalseTest
     */
    public function testIsTaskFinished($returnValue, $expectedValue)
    {
        $this->_stateFlagMock->expects($this->once())->method('isTaskFinished')->will($this->returnValue($returnValue));
        $this->assertEquals($expectedValue, $this->_helperModel->isTaskFinished());
    }

    public function testSaveTaskAsQueued()
    {
        $this->_stateFlagMock->expects($this->once())->method('saveAsQueued')->will($this->returnSelf());
        $this->_helperModel->saveTaskAsQueued();
    }

    public function testSaveTaskAsProcessing()
    {
        $this->_stateFlagMock->expects($this->once())->method('saveAsProcessing')->will($this->returnSelf());
        $this->_helperModel->saveTaskAsProcessing();
    }

    public function testSaveTaskAsFinished()
    {
        $this->_stateFlagMock->expects($this->once())->method('saveAsFinished')->will($this->returnSelf());
        $this->_helperModel->saveTaskAsFinished();
    }

    public function testSaveTaskAsNotified()
    {
        $this->_stateFlagMock->expects($this->once())->method('saveAsNotified')->will($this->returnSelf());
        $this->_helperModel->saveTaskAsNotified();
    }
}

/**
 * Stub Class for Saas_ImportExport_Helper_StateAbstract
 */
class Stub_Saas_ImportExport_Helper_StateAbstract extends Saas_ImportExport_Helper_StateAbstract
{
    public function onValidationShutdown()
    {
    }
}
