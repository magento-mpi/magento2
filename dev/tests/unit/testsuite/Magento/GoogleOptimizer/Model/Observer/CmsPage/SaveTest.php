<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\GoogleOptimizer\Model\Observer\CmsPage;

class SaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_pageMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_codeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;

    /**
     * @var \Magento\GoogleOptimizer\Model\Observer\CmsPage\Save
     */
    protected $_modelObserver;

    /**
     * @var int
     */
    protected $_storeId;

    protected function setUp()
    {
        $this->_helperMock = $this->getMock('Magento\GoogleOptimizer\Helper\Data', array(), array(), '', false);
        $this->_codeMock = $this->getMock('Magento\GoogleOptimizer\Model\Code', array(), array(), '', false);
        $this->_requestMock = $this->getMock('Magento\App\RequestInterface', array(), array(), '', false);

        $this->_pageMock = $this->getMock('Magento\Cms\Model\Page', array(), array(), '', false);
        $event = $this->getMock('Magento\Event', array('getObject'), array(), '', false);
        $event->expects($this->once())->method('getObject')->will($this->returnValue($this->_pageMock));
        $this->_eventObserverMock = $this->getMock('Magento\Event\Observer', array(), array(), '', false);
        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_modelObserver = $objectManagerHelper->getObject(
            'Magento\GoogleOptimizer\Model\Observer\CmsPage\Save',
            array('helper' => $this->_helperMock, 'modelCode' => $this->_codeMock, 'request' => $this->_requestMock)
        );
    }

    public function testCreatingCodeIfRequestIsValid()
    {
        $pageId = 3;
        $experimentScript = 'some string';

        $this->_pageMock->expects($this->once())->method('getId')->will($this->returnValue($pageId));
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'google_experiment'
        )->will(
            $this->returnValue(array('code_id' => '', 'experiment_script' => $experimentScript))
        );

        $this->_codeMock->expects(
            $this->once()
        )->method(
            'addData'
        )->with(
            array(
                'entity_type' => \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PAGE,
                'entity_id' => $pageId,
                'store_id' => 0,
                'experiment_script' => $experimentScript
            )
        );
        $this->_codeMock->expects($this->once())->method('save');

        $this->_modelObserver->saveGoogleExperimentScript($this->_eventObserverMock);
    }

    /**
     * @param array $params
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Wrong request parameters
     * @dataProvider dataProviderWrongRequestForCreating
     */
    public function testCreatingCodeIfRequestIsNotValid($params)
    {
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'google_experiment'
        )->will(
            $this->returnValue($params)
        );

        $this->_modelObserver->saveGoogleExperimentScript($this->_eventObserverMock);
    }

    /**
     * @return array
     */
    public function dataProviderWrongRequestForCreating()
    {
        return array(array('wrong type'), array(array('code_id' => '')), array(array('experiment_script' => '')));
    }

    public function testEditingCodeIfRequestIsValid()
    {
        $pageId = 3;
        $experimentScript = 'some string';
        $codeId = 5;

        $this->_pageMock->expects($this->once())->method('getId')->will($this->returnValue($pageId));
        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'google_experiment'
        )->will(
            $this->returnValue(array('code_id' => $codeId, 'experiment_script' => $experimentScript))
        );

        $this->_codeMock->expects($this->once())->method('load')->with($codeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue($codeId));

        $this->_codeMock->expects(
            $this->once()
        )->method(
            'addData'
        )->with(
            array(
                'entity_type' => \Magento\GoogleOptimizer\Model\Code::ENTITY_TYPE_PAGE,
                'entity_id' => $pageId,
                'store_id' => $this->_storeId,
                'experiment_script' => $experimentScript
            )
        );
        $this->_codeMock->expects($this->once())->method('save');

        $this->_modelObserver->saveGoogleExperimentScript($this->_eventObserverMock);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Code does not exist
     */
    public function testEditingCodeIfCodeModelIsNotFound()
    {
        $experimentScript = 'some string';
        $codeId = 5;

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'google_experiment'
        )->will(
            $this->returnValue(array('code_id' => $codeId, 'experiment_script' => $experimentScript))
        );

        $this->_codeMock->expects($this->once())->method('load')->with($codeId);
        $this->_codeMock->expects($this->atLeastOnce())->method('getId')->will($this->returnValue(false));
        $this->_codeMock->expects($this->never())->method('save');

        $this->_modelObserver->saveGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testRemovingCode()
    {
        $codeId = 5;

        $this->_helperMock->expects(
            $this->once()
        )->method(
            'isGoogleExperimentActive'
        )->with(
            $this->_storeId
        )->will(
            $this->returnValue(true)
        );

        $this->_requestMock->expects(
            $this->once()
        )->method(
            'getParam'
        )->with(
            'google_experiment'
        )->will(
            $this->returnValue(array('code_id' => $codeId, 'experiment_script' => ''))
        );

        $this->_codeMock->expects($this->once())->method('load')->with($codeId);
        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue($codeId));

        $this->_codeMock->expects($this->never())->method('save');
        $this->_codeMock->expects($this->once())->method('delete');

        $this->_modelObserver->saveGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testManagingCodeIfGoogleExperimentIsDisabled()
    {
        $this->_helperMock->expects(
            $this->once()
        )->method(
            'isGoogleExperimentActive'
        )->with(
            $this->_storeId
        )->will(
            $this->returnValue(false)
        );
        $this->_codeMock->expects($this->never())->method('load');
        $this->_codeMock->expects($this->never())->method('save');
        $this->_codeMock->expects($this->never())->method('delete');

        $this->_modelObserver->saveGoogleExperimentScript($this->_eventObserverMock);
    }
}
