<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Locale\Hierarchy;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Locale\Hierarchy\Config
     */
    protected $_model;

    /**
     * @var \Magento\Core\Model\Locale\Hierarchy\Config\Reader
     */
    protected $_configReaderMock;

    /**
     * @var \Magento\Config\CacheInterface
     */
    protected $_cacheMock;

    /**
     * @var string
     */
    protected $_cacheId;

    /**
     * @var array
     */
    protected $_testData;

    protected function setUp()
    {
        $this->_configReaderMock = $this->getMock(
            'Magento\Core\Model\Locale\Hierarchy\Config\Reader', array(), array(), '', false
        );
        $this->_cacheMock = $this->getMock('Magento\Config\CacheInterface');
        $this->_cacheId = 'customCacheId';

        $this->_testData = array('key' => 'value');

        $this->_cacheMock->expects($this->once())
            ->method('load')
            ->with($this->_cacheId)
            ->will($this->returnValue(serialize($this->_testData)));

        $this->_model = new \Magento\Core\Model\Locale\Hierarchy\Config(
            $this->_configReaderMock,
            $this->_cacheMock,
            $this->_cacheId
        );
    }

    public function testGetHierarchy()
    {
        $this->assertEquals($this->_testData, $this->_model->getHierarchy());
    }
}
