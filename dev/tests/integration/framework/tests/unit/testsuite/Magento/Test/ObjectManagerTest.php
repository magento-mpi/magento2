<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\ObjectManager\Test
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
        $verification = $this->getMock(
            'Magento\Framework\App\Filesystem\DirectoryList\Verification',
            array(),
            array(),
            '',
            false
        );
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
        $factory = $this->getMock('\Magento\ObjectManager\Factory', array(), array(), '', false);
        $factory->expects($this->exactly(2))->method('create')->will(
            $this->returnCallback(
                function ($className) {
                    if ($className === 'Magento\Object') {
                        return $this->getMock('Magento\Object', array(), array(), '', false);
                    }
                }
            )
        );

        $model = new \Magento\TestFramework\ObjectManager(
            $factory,
            $instanceConfig,
            array(
                'Magento\Framework\App\Filesystem\DirectoryList\Verification' => $verification,
                'Magento\Framework\App\Cache\Type\Config' => $cache,
                'Magento\Framework\App\ObjectManager\ConfigLoader' => $configLoader,
                'Magento\Framework\App\ObjectManager\ConfigCache' => $configCache,
                'Magento\Config\ReaderInterface' => $this->getMock('Magento\Config\ReaderInterface'),
                'Magento\Config\ScopeInterface' => $this->getMock('Magento\Config\ScopeInterface'),
                'Magento\Config\CacheInterface' => $this->getMock('Magento\Config\CacheInterface'),
                'Magento\Cache\FrontendInterface' => $this->getMock('Magento\Cache\FrontendInterface'),
                'Magento\Framework\App\Resource' => $this->getMock('Magento\Framework\App\Resource', array(), array(), '', false),
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
        $instance1 = $model->get('Magento\Object');

        $this->assertSame($instance1, $model->get('Magento\Object'));
        $this->assertSame($model, $model->clearCache());
        $this->assertSame($model, $model->get('Magento\ObjectManager'));
        $this->assertSame($resource, $model->get('Magento\Framework\App\Resource'));
        $this->assertNotSame($instance1, $model->get('Magento\Object'));
    }
}
