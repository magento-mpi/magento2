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
     * @var \Magento\Core\Model\File\Storage\File
     */
    protected $_model;
    /**
     * @var \Magento\Core\Helper\File\Media
     */
    protected $_loggerMock;

    /**
     * @var \Magento\Core\Helper\File\Storage\Database
     */
    protected $_storageHelperMock;

    /**
     * @var \Magento\Stdlib\DateTime\DateTime
     */
    protected $_mediaHelperMock;

    /**
     * @var \Magento\Core\Model\Resource\File\Storage\File
     */
    protected $_fileUtilityMock;

    protected function setUp()
    {
        $this->_loggerMock =
            $this->getMock('Magento\Logger', array(), array(), '', false);
        $this->_storageHelperMock =
            $this->getMock('Magento\Core\Helper\File\Storage\Database', array(), array(), '', false);
        $this->_mediaHelperMock =
            $this->getMock('Magento\Core\Helper\File\Media', array(), array(), '', false);
        $this->_fileUtilityMock =
            $this->getMock('Magento\Core\Model\Resource\File\Storage\File', array(), array(), '', false);

        $this->_model = new \Magento\Core\Model\File\Storage\File(
            $this->_loggerMock,
            $this->_storageHelperMock,
            $this->_mediaHelperMock,
            $this->_fileUtilityMock
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
