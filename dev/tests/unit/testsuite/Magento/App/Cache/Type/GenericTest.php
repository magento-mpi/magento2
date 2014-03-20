<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * The test covers \Magento\App\Cache_Type_* classes all at once, as all of them are similar
 */
namespace Magento\App\Cache\Type;

class GenericTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $className
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($className)
    {
        $frontendMock = $this->getMock('Magento\Cache\FrontendInterface');

        $poolMock = $this->getMock('Magento\App\Cache\Type\FrontendPool', array(), array(), '', false);
        $poolMock->expects(
            $this->atLeastOnce()
        )->method(
            'get'
        )->with(
            $className::TYPE_IDENTIFIER
        )->will(
            $this->returnValue($frontendMock)
        );

        $model = new $className($poolMock);

        // Test initialization was done right
        $this->assertEquals($className::CACHE_TAG, $model->getTag(), 'The tag is wrong');

        // Test that frontend is now engaged in operations
        $frontendMock->expects($this->once())->method('load')->with(26);
        $model->load(26);
    }

    /**
     * @return array
     */
    public static function constructorDataProvider()
    {
        return array(
            array('Magento\App\Cache\Type\Block'),
            array('Magento\App\Cache\Type\Collection'),
            array('Magento\App\Cache\Type\Config'),
            array('Magento\App\Cache\Type\Layout'),
            array('Magento\App\Cache\Type\Translate'),
            array('Magento\App\Cache\Type\Block')
        );
    }
}
