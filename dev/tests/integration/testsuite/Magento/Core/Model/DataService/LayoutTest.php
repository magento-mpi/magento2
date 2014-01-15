<?php
/**
 * Set of tests of layout directives handling behavior
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\DataService;

class LayoutTest extends \Magento\TestFramework\TestCase\AbstractController
{
    private $_dataServiceGraph;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Filesystem
     */
    protected $filesystem;

    /**
     * Setup
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $rootPath = $this->objectManager->get('Magento\Filesystem')
            ->getDirectoryRead(\Magento\Filesystem::ROOT)
            ->getAbsolutePath();

        $path = str_replace('\\', '/', realpath(__DIR__ . '/../DataService/LayoutTest'));
        $directoryList = new \Magento\Filesystem\DirectoryList(
            $rootPath,
            array(\Magento\Filesystem::MODULES => array('path' => $path))
        );

        $this->filesystem = new \Magento\Filesystem(
            $directoryList,
            new \Magento\Filesystem\Directory\ReadFactory(),
            new \Magento\Filesystem\Directory\WriteFactory()
        );

        $config = $this->_loadServiceCallsConfig();
        parent::setUp();
        $this->dispatch("catalog/category/view/foo/bar");
        $fixtureFileName = __DIR__ . '/LayoutTest/Magento/Catalog/Service/TestProduct.php';
        include $fixtureFileName;
        $invoker = $this->objectManager->create(
            'Magento\Core\Model\DataService\Invoker',
            array('config' => $config)
        );
        /** @var \Magento\Core\Model\DataService\Graph $dataServiceGraph */
        $this->_dataServiceGraph = $this->objectManager->create(
            'Magento\Core\Model\DataService\Graph',
            array('dataServiceInvoker' => $invoker)
        );
    }

    protected function _loadServiceCallsConfig()
    {
        $modulesDir = new \Magento\Module\Dir(
            $this->filesystem,
            $this->objectManager->get('Magento\Stdlib\String')
        );
        /** @var \Magento\Module\Dir\Reader $moduleReader */

        $moduleReader = new \Magento\Module\Dir\Reader(
            $modulesDir,
            $this->objectManager->get('Magento\Module\ModuleListInterface'),
            $this->filesystem,
            $this->objectManager->get('Magento\Config\FileIteratorFactory')
        );

        /** @var \Magento\Core\Model\DataService\Config\Reader\Factory $dsCfgReaderFactory */
        $dsCfgReaderFactory = $this->objectManager->create(
            'Magento\Core\Model\DataService\Config\Reader\Factory'
        );

        /** @var \Magento\Core\Model\DataService\Config $config */
        $dataServiceConfig = new \Magento\Core\Model\DataService\Config(
            $dsCfgReaderFactory,
            $moduleReader
        );
        return $dataServiceConfig;
    }

    /**
     * Test Layout initialization of service calls
     */
    public function testServiceCalls()
    {
        /** @var \Magento\View\LayoutInterface $layout */
        $layout = $this->_getLayoutModel('layout_update.xml');
        $serviceCalls = $layout->getServiceCalls();
        $expectedServiceCalls = array(
            'testServiceCall' => array(
                'namespaces' => array(
                    'block_with_service_calls' => 'testData'
                )
            )
        );
        $this->assertEquals($expectedServiceCalls, $serviceCalls);
        $dictionary = $this->_dataServiceGraph->getByNamespace('block_with_service_calls');
        $expectedDictionary = array(
            'testData' => array(
                'testProduct' => array(
                    'id' => 'bar'
                )
            )
        );
        $this->assertEquals($expectedDictionary, $dictionary);
    }

    /**
     * Prepare a layout model with pre-loaded fixture of an update XML
     *
     * @param string $fixtureFile
     *
     * @return \Magento\View\LayoutInterface
     */
    protected function _getLayoutModel($fixtureFile)
    {
        /** @var $layout \Magento\View\LayoutInterface */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\View\LayoutInterface',
            array('dataServiceGraph' => $this->_dataServiceGraph)
        );
        $xml = simplexml_load_file(__DIR__ . "/LayoutTest/{$fixtureFile}", 'Magento\View\Layout\Element');
        $layout->setXml($xml);
        $layout->generateElements();
        return $layout;
    }
}
