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

class LayoutTest extends \Magento\TestFramework\TestCase\ControllerAbstract
{
    private $_dataServiceGraph;

    public function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        // Need to call this first so we get proper config
        $config = $this->_loadServiceCallsConfig();
        parent::setUp();
        $this->dispatch("catalog/category/view/foo/bar");
        $fixtureFileName = __DIR__ . DS . "LayoutTest" . DS . 'Magento' . DS . 'Catalog' . DS . 'Service'
            . DS . 'TestProduct.php';
        include $fixtureFileName;
        $invoker = $objectManager->create(
            'Magento\Core\Model\DataService\Invoker',
            array('config' => $config)
        );
        /** @var \Magento\Core\Model\DataService\Graph $dataServiceGraph */
        $this->_dataServiceGraph = $objectManager->create(
            'Magento\Core\Model\DataService\Graph',
            array('dataServiceInvoker' => $invoker)
        );
    }

    protected function _loadServiceCallsConfig()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Magento\Core\Model\Dir $dirs */
        $dirs = $objectManager->create(
            'Magento\Core\Model\Dir',
            array(
                'baseDir' => BP,
                'dirs' => array(\Magento\Core\Model\Dir::MODULES => __DIR__ . '/LayoutTest')
            )
        );

        /** @var \Magento\Core\Model\Config\Modules\Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Magento\Core\Model\Config\Modules\Reader',
            array(
                'dirs' => $dirs,
            )
        );

        /** @var \Magento\Core\Model\DataService\Config\Reader\Factory $dsCfgReaderFactory */
        $dsCfgReaderFactory = $objectManager->create(
            'Magento\Core\Model\DataService\Config\Reader\Factory'
        );

        /** @var \Magento\Core\Model\DataService\Config $config */
        $dataServiceConfig = new \Magento\Core\Model\DataService\Config($dsCfgReaderFactory, $moduleReader);
        return $dataServiceConfig;
    }

    /**
     * Test Layout initialization of service calls
     */
    public function testServiceCalls()
    {
        /** @var \Magento\Core\Model\Layout $layout */
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
     * @return \Magento\Core\Model\Layout
     */
    protected function _getLayoutModel($fixtureFile)
    {
        /** @var $layout \Magento\Core\Model\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Layout',
            array('dataServiceGraph' => $this->_dataServiceGraph)
        );
        $xml = simplexml_load_file(__DIR__ . "/LayoutTest/{$fixtureFile}", 'Magento\Core\Model\Layout\Element');
        $layout->setXml($xml);
        $layout->generateElements();
        return $layout;
    }
}
