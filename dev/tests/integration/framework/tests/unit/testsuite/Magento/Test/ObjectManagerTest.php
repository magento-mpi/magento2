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
    protected $_instanceCache = array(
        'hashShort' => array(),
        'hashLong'  => array()
    );

    public function testClearCache()
    {
        $resource = new \stdClass;
        $instanceConfig = new \Magento\TestFramework\ObjectManager\Config();
        $verification = $this->getMock('Magento\App\Filesystem\DirectoryList\Verification', array(), array(), '', false);
        $cache = $this->getMock('Magento\App\CacheInterface');
        $configLoader = $this->getMock('Magento\App\ObjectManager\ConfigLoader', array(), array(), '', false);
        $configCache = $this->getMock('Magento\App\ObjectManager\ConfigCache', array(), array(), '', false);
        $primaryLoaderMock = $this->getMock(
            'Magento\App\ObjectManager\ConfigLoader\Primary', array(), array(), '', false
        );

        $model = new \Magento\TestFramework\ObjectManager(
            null, $instanceConfig,
            array(
                'Magento\App\Filesystem\DirectoryList\Verification' => $verification,
                'Magento\App\Cache\Type\Config' => $cache,
                'Magento\App\ObjectManager\ConfigLoader' => $configLoader,
                'Magento\App\ObjectManager\ConfigCache' => $configCache,
                'Magento\Config\ReaderInterface' => $this->getMock('Magento\Config\ReaderInterface'),
                'Magento\Config\ScopeInterface' => $this->getMock('Magento\Config\ScopeInterface'),
                'Magento\Config\CacheInterface' => $this->getMock('Magento\Config\CacheInterface'),
                'Magento\Cache\FrontendInterface' => $this->getMock('Magento\Cache\FrontendInterface'),
                'Magento\App\Resource' => $this->getMock(
                    'Magento\App\Resource', array(), array(), '', false
                ),
                'Magento\App\Resource\Config' => $this->getMock(
                    'Magento\App\Resource\Config', array(), array(), '', false
                ),
            ),
            $primaryLoaderMock
        );

        $model->addSharedInstance($resource, 'Magento\App\Resource');
        $instance1 = $model->get('Magento\Object');

        $this->assertSame($instance1, $model->get('Magento\Object'));
        $this->assertSame($model, $model->clearCache());
        $this->assertSame($model, $model->get('Magento\ObjectManager'));
        $this->assertSame($resource, $model->get('Magento\App\Resource'));
        $this->assertNotSame($instance1, $model->get('Magento\Object'));
    }
}
