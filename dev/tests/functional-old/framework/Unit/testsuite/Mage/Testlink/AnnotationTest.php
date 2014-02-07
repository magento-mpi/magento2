<?php

/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_Testlink_AnnotationTest extends Unit_PHPUnit_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_annotationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_listenerMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_connectorMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_testCaseMock;

    /**
     * @var PHPUnit_Framework_TestResult
     */
    protected $_testResultMock;

    protected function setUp()
    {
        $this->_connectorMock = $this->getMock('Mage_Testlink_Connector', array(), array(), '', false);
        $this->_testCaseMock = $this->getMock('PHPUnit_Framework_TestCase', array(), array(), '', false);
        $this->_listenerMock = $this->getMock('Mage_Testlink_Listener', array(), array(), '', false);
        $this->_listenerMock->expects($this->any())->method('getCurrentTest')
            ->will($this->returnValue($this->_testCaseMock));
        $this->assertTrue(class_exists('Mage_Testlink_AnnotationMock'));
        $this->_annotationMock = new Mage_Testlink_AnnotationMock();
        $this->_testResultMock = $this->getMock(
            'PHPUnit_Framework_TestResult',
            array('__construct', 'getTestResultObject'),
            array(),
            '',
            false
        );
        $this->_testCaseMock->expects($this->any())->method('getTestResultObject')
            ->will($this->returnValue($this->_testResultMock));
    }

    /**
     * @covers Mage_Testlink_Annotation::startTest
     */
    public function testStartTest()
    {
        $this->_annotationMock->__call('setListener', array($this->_listenerMock));
        $this->_listenerMock->expects($this->any())->method('getCurrentTest')
            ->will($this->returnValue($this->_testCaseMock));
        $this->assertNull($this->_annotationMock->__call('startTest', array()));
    }

    /**
     * @covers Mage_Testlink_Annotation::testFailed
     */
    public function testTestFailed()
    {
        $this->_testCaseMock->expects($this->never())->method('getAnnotations');
        $this->_connectorMock->expects($this->never())->method('report');
        $this->assertNull($this->_annotationMock->testFailed());

    }

    /**
     * @covers Mage_Testlink_Annotation::testSkipped
     */
    public function testTestSkipped()
    {
        $this->_testCaseMock->expects($this->never())->method('getAnnotations');
        $this->_connectorMock->expects($this->never())->method('report');
        $this->assertNull($this->_annotationMock->testSkipped());
    }

    /**
     * @covers Mage_Testlink_Annotation::endTest
     */
    public function testEndTest()
    {
        $this->_testCaseMock->expects($this->never())->method('getAnnotations');
        $this->_connectorMock->expects($this->never())->method('report');
        $this->assertNull($this->_annotationMock->endTest());
    }
}
