<?php
/**
 * {license_notice}
 *
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_PageCache_Model_Cache_TypeTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $frontendMock = $this->getMock('Magento_Cache_FrontendInterface');

        $poolMock = $this->getMock('Mage_Core_Model_Cache_Type_FrontendPool', array(), array(), '', false);
        $poolMock->expects($this->atLeastOnce())
            ->method('get')
            ->with(Enterprise_PageCache_Model_Cache_Type::TYPE_IDENTIFIER)
            ->will($this->returnValue($frontendMock));

        $model = new Enterprise_PageCache_Model_Cache_Type($poolMock);

        // Test initialization was done right
        $this->assertEquals(Enterprise_PageCache_Model_Cache_Type::CACHE_TAG, $model->getTag(), 'The tag is wrong');

        // Test that frontend is now engaged in operations
        $frontendMock->expects($this->once())
            ->method('load')
            ->with(26);
        $model->load(26);
    }
}
