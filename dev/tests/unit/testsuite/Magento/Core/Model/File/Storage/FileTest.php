<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Core\Model\File\Storage;

/**
 * Class MediaTest
 */
class MediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Helper\File\Media
     */
    protected $_helper;

    /**
     * @var \Magento\Core\Helper\Context
     */
    protected $_contextMock;

    /**
     * @var \Magento\Core\Model\Date
     */
    protected $_dateMock;

    protected function setUp()
    {
        $this->_contextMock =
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false);
        $this->_dateMock =
            $this->getMock('Magento\Core\Model\Date', array(), array(), '', false);

        $this->_model = new \Magento\Core\Helper\File\Media(
            $this->_contextMock,
            $this->_dateMock
        );
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    public function testCollectDataSuccess()
    {
        $this->_fileUtilityMock
            ->expects($this->any())
            ->method('getStorageData')
            ->will($this->returnValue(array('files' => array('value1', 'value2'))));
        $this->assertEmpty(array_diff($this->_model->collectData(0, 1), array('value1')));
    }

    public function testCollectDataFailureWrongType()
    {
        $this->_fileUtilityMock
            ->expects($this->any())
            ->method('getStorageData')
            ->will($this->returnValue(array('files' => array('value1', 'value2'))));
        $this->assertFalse($this->_model->collectData(0, 1, 'some-wrong-key'));
    }

    public function testCollectDataFailureEmptyDataWasGiven()
    {
        $this->_fileUtilityMock
            ->expects($this->any())
            ->method('getStorageData')
            ->will($this->returnValue(array('files' => array())));
        $this->assertFalse($this->_model->collectData(0, 1));
    }
}
