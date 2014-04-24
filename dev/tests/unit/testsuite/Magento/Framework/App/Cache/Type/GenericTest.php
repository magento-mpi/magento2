<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * The test covers \Magento\Framework\App\Cache_Type_* classes all at once, as all of them are similar
 */
namespace Magento\Framework\App\Cache\Type;

class GenericTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $className
     * @dataProvider constructorDataProvider
     */
    public function testConstructor($className)
    {
        $frontendMock = $this->getMock('Magento\Framework\Cache\FrontendInterface');

        $poolMock = $this->getMock('Magento\Framework\App\Cache\Type\FrontendPool', array(), array(), '', false);
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
            array('Magento\Framework\App\Cache\Type\Block'),
            array('Magento\Framework\App\Cache\Type\Collection'),
            array('Magento\Framework\App\Cache\Type\Config'),
            array('Magento\Framework\App\Cache\Type\Layout'),
            array('Magento\Framework\App\Cache\Type\Translate'),
            array('Magento\Framework\App\Cache\Type\Block')
        );
    }
}
