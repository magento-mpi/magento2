<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * The test covers Mage_Core_Model_Cache_Type_* classes all at once, as all of them are similar
 */
class Mage_Core_Model_Cache_Type_GenericTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $className
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($className)
    {
        $frontendMock = $this->getMock('Magento_Cache_FrontendInterface');

        $poolMock = $this->getMock('Mage_Core_Model_Cache_Type_FrontendPool', array(), array(), '', false);
        $poolMock->expects($this->atLeastOnce())
            ->method('get')
            ->with($className::TYPE_IDENTIFIER)
            ->will($this->returnValue($frontendMock));

        $model = new $className($poolMock);

        // Test initialization was done right
        $this->assertEquals($className::CACHE_TAG, $model->getTag(), 'The tag is wrong');

        // Test that frontend is now engaged in operations
        $frontendMock->expects($this->once())
            ->method('load')
            ->with(26);
        $model->load(26);
    }

    /**
     * @return array
     */
    public static function constructorDataProvider()
    {
        return array(
            array('Mage_Core_Model_Cache_Type_Block'),
            array('Mage_Core_Model_Cache_Type_Collection'),
            array('Mage_Core_Model_Cache_Type_Config'),
            array('Mage_Core_Model_Cache_Type_Layout'),
            array('Mage_Core_Model_Cache_Type_Translate'),
            array('Mage_Core_Model_Cache_Type_Block'),
        );
    }
}
