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
        $dirs = $this->getMock('Magento\App\Dir', array(), array(), '', false);
        $verification = $this->getMock('Magento\App\Dir\Verification', array(), array(), '', false);
        $cache = $this->getMock('Magento\Core\Model\CacheInterface');
        $configLoader = $this->getMock('Magento\App\ObjectManager\ConfigLoader', array(), array(), '', false);
        $configLoader->expects($this->once())->method('load')->will($this->returnValue(array()));
        $configCache = $this->getMock('Magento\App\ObjectManager\ConfigCache', array(), array(), '', false);
        $primaryLoaderMock = $this->getMock(
            'Magento\App\ObjectManager\ConfigLoader\Primary', array(), array(), '', false
        );

        $model = new \Magento\TestFramework\ObjectManager(
            $primaryConfig, $instanceConfig,
            array(
                'Magento\App\Dir\Verification' => $verification,
                'Magento\Core\Model\Cache\Type\Config' => $cache,
                'Magento\App\ObjectManager\ConfigLoader' => $configLoader,
                'Magento\App\ObjectManager\ConfigCache' => $configCache,
                'Magento\Config\ReaderInterface' => $this->getMock('Magento\Config\ReaderInterface'),
                'Magento\Config\ScopeInterface' => $this->getMock('Magento\Config\ScopeInterface'),
                'Magento\Config\CacheInterface' => $this->getMock('Magento\Config\CacheInterface'),
                'Magento\Cache\FrontendInterface' => $this->getMock('Magento\Cache\FrontendInterface'),
                'Magento\Core\Model\Resource' => $this->getMock(
                    'Magento\Core\Model\Resource', array(), array(), '', false
                ),
                'Magento\Core\Model\Config\Resource' => $this->getMock(
                    'Magento\Core\Model\Config\Resource', array(), array(), '', false
                ),
            ),
            $primaryLoaderMock
        );

        $model->addSharedInstance($resource, 'Magento\Core\Model\Resource');
        $instance1 = $model->get('Magento\Object');

        $this->assertSame($instance1, $model->get('Magento\Object'));
        $this->assertSame($model, $model->clearCache());
        $this->assertSame($model, $model->get('Magento\ObjectManager'));
        $this->assertSame($resource, $model->get('Magento\Core\Model\Resource'));
        $this->assertNotSame($instance1, $model->get('Magento\Object'));
    }
}
