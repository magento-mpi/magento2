<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * The test covers Magento_Core_Model_Cache_Type_* classes all at once, as all of them are similar
 */
namespace Magento\Core\Model\Cache\Type;

class GenericTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $className
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($className)
    {
        $frontendMock = $this->getMock('Magento\Cache\FrontendInterface');

        $poolMock = $this->getMock('Magento\Core\Model\Cache\Type\FrontendPool', array(), array(), '', false);
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
            array('Magento\Core\Model\Cache\Type\Block'),
            array('Magento\Core\Model\Cache\Type\Collection'),
            array('Magento\Core\Model\Cache\Type\Config'),
            array('Magento\Core\Model\Cache\Type\Layout'),
            array('Magento\Core\Model\Cache\Type\Translate'),
            array('Magento\Core\Model\Cache\Type\Block'),
        );
    }
}
