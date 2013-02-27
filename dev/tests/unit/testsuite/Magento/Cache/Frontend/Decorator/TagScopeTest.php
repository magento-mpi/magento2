<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Cache_Frontend_Decorator_TagScopeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_tagForTests = 'bird';

    /**
     * @var CacheScopeCleanVerification
     */
    protected $_cache;

    public function setUp()
    {
        include_once __DIR__ . '/_files/CacheScopeCleanVerification.php';
        $this->_cache = new CacheScopeCleanVerification();
    }

    /**
     * @param string $mode
     * @param array $tags
     * @param array $expectedRecordsLeft
     * @dataProvider cleanDataProvider
     */
    public function testClean($mode, $tags, $expectedRecordsLeft)
    {
        if ($mode == Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG) {
            $this->markTestIncomplete('MAGETWO-8033');
        }
        if ($mode == Zend_Cache::CLEANING_MODE_OLD) {
            $this->markTestIncomplete('MAGETWO-8034');
        }

        $frontend = $this->getMock('Magento_Cache_FrontendInterface');
        $frontend->expects($this->any())
            ->method('clean')
            ->will($this->returnCallback(array($this->_cache, 'clean')));

        $object = new Magento_Cache_Frontend_Decorator_TagScope($frontend, $this->_tagForTests);
        $object->clean($mode, $tags);
        $this->assertEquals($expectedRecordsLeft, $this->_cache->getRecordIds());
    }

    public function cleanDataProvider()
    {
        return array(
            Zend_Cache::CLEANING_MODE_ALL => array(
                Zend_Cache::CLEANING_MODE_ALL,
                array(),
                array('elephant', 'man', 'raccoon')
            ),
            'tags must be ignored in CLEANING_MODE_ALL' => array(
                Zend_Cache::CLEANING_MODE_ALL,
                array('big'),
                array('elephant', 'man', 'raccoon')
            ),
            Zend_Cache::CLEANING_MODE_MATCHING_TAG => array(
                Zend_Cache::CLEANING_MODE_MATCHING_TAG,
                array('big'),
                array('elephant', 'man', 'raccoon', 'turkey', 'pigeon')
            ),
            Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG => array(
                Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG,
                array('big', 'small'),
                array('elephant', 'man', 'raccoon', 'turkey')
            ),
            Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG => array(
                Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG,
                array('big', 'small'),
                array('elephant', 'man', 'raccoon', 'ostrich', 'pigeon')
            ),
            Zend_Cache::CLEANING_MODE_OLD => array(
                Zend_Cache::CLEANING_MODE_OLD,
                array(),
                array('elephant', 'man', 'raccoon', 'ostrich', 'pigeon')
            ),
        );
    }
}
