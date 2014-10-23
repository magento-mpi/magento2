<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\Framework\ObjectManager\Test
 */
namespace Magento\Test;

class ObjectManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Expected instance manager parametrized cache after clear
     *
     * @var array
     */
    protected $_instanceCache = array('hashShort' => array(), 'hashLong' => array());

    public function testClearCache()
    {
        $resource = new \stdClass();
        $instanceConfig = new \Magento\TestFramework\ObjectManager\Config();
        $cache = $this->getMock('Magento\Framework\App\CacheInterface');
        $configLoader = $this->getMock('Magento\Framework\App\ObjectManager\ConfigLoader', array(), array(), '', false);
        $configCache = $this->getMock('Magento\Framework\App\ObjectManager\ConfigCache', array(), array(), '', false);
        $primaryLoaderMock = $this->getMock(
            'Magento\Framework\App\ObjectManager\ConfigLoader\Primary',
            array(),
            array(),
            '',
            false
        );
        $factory = $this->getMock('\Magento\Framework\ObjectManager\Factory', array(), array(), '', false);
        $factory->expects($this->exactly(2))->method('create')->will(
            $this->returnCallback(
                function ($className) {
                    if ($className === 'Magento\Framework\Object') {
                        return $this->getMock('Magento\Framework\Object', array(), array(), '', false);
                    }
                }
            )
        );

        $model = new \Magento\TestFramework\ObjectManager(
            $factory,
            $instanceConfig,
            array(
                'Magento\Framework\App\Cache\Type\Config' => $cache,
                'Magento\Framework\App\ObjectManager\ConfigLoader' => $configLoader,
                'Magento\Framework\App\ObjectManager\ConfigCache' => $configCache,
                'Magento\Framework\Config\ReaderInterface' => $this->getMock(
                    'Magento\Framework\Config\ReaderInterface'
                ),
                'Magento\Framework\Config\ScopeInterface' => $this->getMock('Magento\Framework\Config\ScopeInterface'),
                'Magento\Framework\Config\CacheInterface' => $this->getMock('Magento\Framework\Config\CacheInterface'),
                'Magento\Framework\Cache\FrontendInterface' =>
                    $this->getMock('Magento\Framework\Cache\FrontendInterface'),
                'Magento\Framework\App\Resource' => $this->getMockBuilder('Magento\Framework\App\Resource')
                        ->disableOriginalConstructor()
                        ->getMock(),
                'Magento\Framework\App\Resource\Config' => $this->getMock(
                    'Magento\Framework\App\Resource\Config',
                    array(),
                    array(),
                    '',
                    false
                )
            ),
            $primaryLoaderMock
        );

        $model->addSharedInstance($resource, 'Magento\Framework\App\Resource');
        $instance1 = $model->get('Magento\Framework\Object');

        $this->assertSame($instance1, $model->get('Magento\Framework\Object'));
        $this->assertSame($model, $model->clearCache());
        $this->assertSame($model, $model->get('Magento\Framework\ObjectManager'));
        $this->assertSame($resource, $model->get('Magento\Framework\App\Resource'));
        $this->assertNotSame($instance1, $model->get('Magento\Framework\Object'));
    }
}
