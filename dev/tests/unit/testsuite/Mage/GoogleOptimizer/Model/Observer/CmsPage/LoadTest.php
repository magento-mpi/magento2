<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_CmsPage_LoadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_codeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pageMock;

    /**
     * @var Mage_GoogleOptimizer_Model_Observer_CmsPage_Load
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_codeMock = $this->getMock('Mage_GoogleOptimizer_Model_Code', array(), array(), '', false);
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $this->_pageMock = $this->getMock(
            'Mage_Catalog_Model_Category',
            array('setGoogleExperiment', 'getId'),
            array(),
            '',
            false
        );
        $event->expects($this->once())->method('getObject')->will($this->returnValue($this->_pageMock));
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_CmsPage_Load', array(
            'helper' => $this->_helperMock,
            'modelCode' => $this->_codeMock,
        ));
    }

    public function testAppendToCategoryGoogleExperimentScriptSuccess()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));
        $this->_pageMock->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $this->_pageMock->expects($this->once())->method('setGoogleExperiment')->with($this->_codeMock);

        $this->_model->appendToCmsPageGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testAppendToCategoryGoogleExperimentScriptFail()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(false));
        $this->_codeMock->expects($this->never())->method('loadByEntityIdAndType');
        $this->_codeMock->expects($this->never())->method('getId');
        $this->_pageMock->expects($this->never())->method('getId');
        $this->_pageMock->expects($this->never())->method('setGoogleExperiment');

        $this->_model->appendToCmsPageGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testAppendToCategoryGoogleExperimentScriptFailSecond()
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $entityId = 3;
        $storeId = 0;

        $this->_codeMock->expects($this->once())->method('loadByEntityIdAndType')
            ->with($entityId, Mage_GoogleOptimizer_Model_Code::ENTITY_TYPE_PAGE, $storeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(false));
        $this->_pageMock->expects($this->once())->method('getId')->will($this->returnValue($entityId));
        $this->_pageMock->expects($this->never())->method('setGoogleExperiment');

        $this->_model->appendToCmsPageGoogleExperimentScript($this->_eventObserverMock);
    }
}
