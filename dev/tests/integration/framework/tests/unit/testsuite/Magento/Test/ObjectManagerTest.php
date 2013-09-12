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
 * Test class for Magento_ObjectManager_Test
 */
class Magento_Test_ObjectManagerTest extends PHPUnit_Framework_TestCase
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
        $resource = new stdClass;
        $instanceConfig = new Magento_TestFramework_ObjectManager_Config();
        $primaryConfig = $this->getMock('Magento\Core\Model\Config\Primary', array(), array(), '', false);
        $dirs = $this->getMock('Magento\Core\Model\Dir', array(), array(), '', false);
        $verification = $this->getMock('Magento\Core\Model\Dir\Verification', array(), array(), '', false);
        $cache = $this->getMock('Magento\Core\Model\CacheInterface');
        $configLoader = $this->getMock('Magento\Core\Model\ObjectManager\ConfigLoader', array(), array(), '', false);
        $configLoader->expects($this->once())->method('load')->will($this->returnValue(array()));
        $configCache = $this->getMock('Magento\Core\Model\ObjectManager\ConfigCache', array(), array(), '', false);
        $primaryConfig->expects($this->any())->method('getDirectories')->will($this->returnValue($dirs));
        $primaryLoaderMock = $this->getMock(
            '\Magento\Core\Model\ObjectManager\ConfigLoader\Primary', array(), array(), '', false
        );

        $model = new Magento_TestFramework_ObjectManager(
            $primaryConfig, $instanceConfig,
            array(
                'Magento\Core\Model\Dir\Verification' => $verification,
                'Magento\Core\Model\Cache\Type\Config' => $cache,
                'Magento\Core\Model\ObjectManager\ConfigLoader' => $configLoader,
                'Magento\Core\Model\ObjectManager\ConfigCache' => $configCache,
                'Magento\Config\ReaderInterface' => $this->getMock('Magento\Config\ReaderInterface'),
                'Magento\Config\ScopeInterface' => $this->getMock('Magento\Config\ScopeInterface'),
                'Magento\Config\CacheInterface' => $this->getMock('Magento\Config\CacheInterface'),
                'Magento\Cache\FrontendInterface' => $this->getMock('Magento\Cache\FrontendInterface'),
            ),
            $primaryLoaderMock
        );

        $model->addSharedInstance($resource, 'Magento\Core\Model\Resource');
        $instance1 = $model->get('Magento_TestFramework_Request');

        $this->assertSame($instance1, $model->get('Magento_TestFramework_Request'));
        $this->assertSame($model, $model->clearCache());
        $this->assertSame($model, $model->get('Magento\ObjectManager'));
        $this->assertSame($resource, $model->get('Magento\Core\Model\Resource'));
        $this->assertNotSame($instance1, $model->get('Magento_TestFramework_Request'));
    }
}
