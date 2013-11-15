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

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\App\Dir $dirs */
        $dirs = $objectManager->create(
            'Magento\App\Dir',
            array(
                'baseDir' => BP,
                'dirs' => array(
                    \Magento\App\Dir::MODULES => __DIR__ . '/LayoutTest',
                    \Magento\App\Dir::CONFIG => __DIR__ . '/LayoutTest',
                )
            )
        );

        $moduleList = $objectManager->create(
            'Magento\Module\ModuleList',
            array(
                'reader' => $objectManager->create(
                    'Magento\Module\Declaration\Reader\Filesystem',
                    array(
                        'fileResolver' => $objectManager->create(
                            'Magento\Module\Declaration\FileResolver',
                            array(
                                'applicationDirs' => $dirs
                            )
                        )
                    )
                ),
                'cache' => $this->getMock('Magento\Config\CacheInterface')
            )
        );

        /** @var \Magento\Module\Dir\Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento\Module\Dir\Reader',
            array(
                'moduleList' => $moduleList
            )
        );
        $moduleReader->setModuleDir('Magento_Last', 'etc', __DIR__ . '/LayoutTest/Magento/Last/etc');

        /** @var \Magento\Core\Model\DataService\Config\Reader\Factory $dsCfgReaderFactory */
        $dsCfgReaderFactory = $objectManager->create('Magento\Core\Model\DataService\Config\Reader\Factory');

        $this->_config = new \Magento\Core\Model\DataService\Config($dsCfgReaderFactory, $moduleReader);
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
