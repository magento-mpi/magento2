<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Model;

class InstallerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Install\Model\Installer
     */
    protected $_model;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * Application chache model
     *
     * @var \Magento\App\CacheInterface
     */
    protected $_cache;

    /**
     * Application config model
     *
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @var \Magento\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Install\Model\Installer\Config
     */
    protected $_installerConfig;

    /**
     * Set up before test
     */
    public function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_cache = $this->getMock('\Magento\App\CacheInterface', array(), array(), '', false);
        $this->_config = $this->getMock('\Magento\App\ReinitableConfigInterface', array(), array(), '', false);
        $this->_cacheState = $this->getMock('\Magento\App\Cache\StateInterface', array(), array(), '', false);
        $this->_cacheTypeList = $this->getMock('\Magento\App\Cache\TypeListInterface', array(), array(), '', false);
        $this->_appState = $this->getMock('\Magento\App\State', array(), array(), '', false);
        $this->_installerConfig = $this->getMock(
            '\Magento\Install\Model\Installer\Config',
            array(),
            array(),
            '',
            false
        );

        $this->_model = $this->_objectManager->getObject(
            'Magento\Install\Model\Installer',
            array(
                'cache' => $this->_cache,
                'config' => $this->_config,
                'cacheState' => $this->_cacheState,
                'cacheTypeList' => $this->_cacheTypeList,
                'appState' => $this->_appState,
                'installerConfig' => $this->_installerConfig
            )
        );
    }

    public function testFinish()
    {
        $cacheTypeListArray = array('one', 'two');

        $this->_cache->expects($this->once())->method('clean');

        $this->_config->expects($this->once())->method('reinit');

        $this->_cacheState->expects($this->once())->method('persist');
        $this->_cacheState->expects($this->exactly(count($cacheTypeListArray)))->method('setEnabled');

        $this->_cacheTypeList->expects(
            $this->once()
        )->method(
            'getTypes'
        )->will(
            $this->returnValue($cacheTypeListArray)
        );

        $this->_appState->expects($this->once())->method('setInstallDate')->with($this->greaterThanOrEqual(date('r')));

        $this->_installerConfig->expects(
            $this->once()
        )->method(
            'replaceTmpInstallDate'
        )->with(
            $this->greaterThanOrEqual(date('r'))
        );

        $this->assertSame($this->_model, $this->_model->finish());
    }
}
