<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model;

class LocaleTest extends \PHPUnit_Framework_TestCase
{
    const DEFAULT_TIME_ZONE = 'America/New_York';

    const TIME_FORMAT_SHORT_ISO = 'h:mm a';

    const DATETIME_FORMAT_SHORT = 'n/j/y g:i A';

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_cacheFrontend;

    /**
     * @var \Magento\Core\Model\App
     */
    protected $_storeManager;

    /**
     * @var \DateTime
     */
    protected $_dateTime;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_cacheFrontend = $this->getMock('\Magento\Cache\FrontendInterface');
        $this->_cache = $this->getMock('\Magento\App\CacheInterface');
        $this->_cache->expects($this->any())->method('getFrontend')->will($this->returnValue($this->_cacheFrontend));

        $this->_storeManager = $this->getMock(
            '\Magento\Core\Model\StoreManager',
            array('getStore', 'getConfig'),
            array(),
            '',
            false
        );

        $this->_dateTime = new \DateTime;
        $this->_dateTime->setTimezone(new \DateTimeZone(self::DEFAULT_TIME_ZONE));
    }

    public function testFormatDate()
    {
        /** @var $locale \Magento\Core\Model\Locale */
        $locale = $this->_objectManager->getObject(
            '\Magento\Core\Model\Locale',
            $this->_getConstructArgsForDateFormatting()
        );

        $this->assertEquals(
            $this->_dateTime->format(self::DATETIME_FORMAT_SHORT),
            $locale->formatDate(null, 'short', true)
        );
    }

    public function testFormatTime()
    {
        /** @var $locale \Magento\Core\Model\Locale */
        $locale = $this->_objectManager->getObject(
            '\Magento\Core\Model\Locale',
            $this->_getConstructArgsForDateFormatting()
        );

        $this->assertEquals(
            $this->_dateTime->format(self::DATETIME_FORMAT_SHORT), $locale->formatTime(null, 'short', true)
        );

        $zendDate = new \Zend_Date($this->_dateTime->format('U'));
        $this->assertEquals(
            $zendDate->toString(self::TIME_FORMAT_SHORT_ISO),
            $locale->formatTime($zendDate, 'short')
        );
    }

    protected function _getConstructArgsForDateFormatting()
    {
        $cache = $this->getMock('Zend_Cache_Core');
        $this->_cacheFrontend->expects($this->once())
            ->method('getLowLevelFrontend')
            ->will($this->returnValue($cache));

        $this->_storeManager->expects($this->once())
            ->method('getStore')
            ->will($this->returnValue($this->_storeManager));

        $this->_storeManager->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue(self::DEFAULT_TIME_ZONE));

        return array('cache' => $this->_cache, 'storeManager' => $this->_storeManager);
    }
}
