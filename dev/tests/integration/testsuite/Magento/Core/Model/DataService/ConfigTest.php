<?php
/**
 * Include verification of overriding service call alias with different classes.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\DataService\Config
     */
    protected $_config;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Setup test
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $rootPath = $this->objectManager->get('Magento\Filesystem')
            ->getDirectoryRead(\Magento\Filesystem::ROOT)
            ->getAbsolutePath();

        $path = str_replace('\\', '/', realpath(__DIR__ . '/../DataService/LayoutTest'));

        $directoryList = new \Magento\Filesystem\DirectoryList(
            $rootPath, array(
            \Magento\Filesystem::MODULES => array('path' => $path),
            \Magento\Filesystem::CONFIG => array('path' => $path)
        ));

        $this->filesystem = new \Magento\Filesystem(
            $directoryList,
            new \Magento\Filesystem\Directory\ReadFactory(),
            new \Magento\Filesystem\Directory\WriteFactory()
        );

        $modulesDir = new \Magento\Module\Dir(
            $this->filesystem,
            $this->objectManager->get('Magento\Stdlib\String')
        );
        /** @var \Magento\Module\Dir\Reader $moduleReader */


        $moduleList = $this->objectManager->create(
            'Magento\Module\ModuleList',
            array(
                'reader' => $this->objectManager->create(
                    'Magento\Module\Declaration\Reader\Filesystem',
                    array(
                        'fileResolver' => $this->objectManager->create(
                            'Magento\Module\Declaration\FileResolver',
                            array(
                                'filesystem' => $this->filesystem
                            )
                        )
                    )
                ),
                'cache' => $this->getMock('Magento\Config\CacheInterface')
            )
        );

        $moduleReader = new \Magento\Module\Dir\Reader(
            $modulesDir,
            $moduleList,
            $this->filesystem,
            $this->objectManager->get('Magento\Config\FileIteratorFactory')
        );

        /** @var \Magento\Core\Model\DataService\Config\Reader\Factory $dsCfgReaderFactory */
        $dsCfgReaderFactory = $this->objectManager->create(
            'Magento\Core\Model\DataService\Config\Reader\Factory'
        );

        /** @var \Magento\Core\Model\DataService\Config $config */
        $this->_config = new \Magento\Core\Model\DataService\Config(
            $dsCfgReaderFactory,
            $moduleReader
        );

    }

    public function testGetClassByAliasOverride()
    {
        $classInfo = $this->_config->getClassByAlias('alias');
        $this->assertEquals('last_service', $classInfo['class']);
        $this->assertEquals('last_method', $classInfo['retrieveMethod']);
        $this->assertEquals('last_value', $classInfo['methodArguments']['last_arg']);
        $this->assertEquals('last_value_two', $classInfo['methodArguments']['last_arg_two']);
    }
}
