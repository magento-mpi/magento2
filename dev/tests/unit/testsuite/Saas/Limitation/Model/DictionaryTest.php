<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_DictionaryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Saas_Limitation_Model_Dictionary
     */
    private $_model;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    private $_translationHelper;

    protected function setUp()
    {
        $this->_translationHelper = $this->getMock('Saas_Limitation_Helper_Data', array('__'), array(), '', false);
        $this->_model = new Saas_Limitation_Model_Dictionary($this->_translationHelper, array(
            'fixture_message' => 'Fixture Message Text',
        ));
    }

    public function testGetMessage()
    {
        $this->_translationHelper
            ->expects($this->once())
            ->method('__')
            ->with('Fixture Message Text')
            ->will($this->returnValue('Translated Message Text'))
        ;
        $this->assertEquals('Translated Message Text', $this->_model->getMessage('fixture_message'));
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Message 'non_existing' has not been defined.
     */
    public function testGetMessageException()
    {
        $this->_model->getMessage('non_existing');
    }
}
