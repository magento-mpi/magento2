<?php
/**
 * Set of tests of layout directives handling behavior
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_DataService_LayoutTest extends Magento_Test_TestCase_ControllerAbstract
{
    private $_dataServiceGraph;

    public function setUp()
    {
        // Need to call this first so we get proper config
        $config = $this->_loadServiceCallsConfig();
        parent::setUp();
        $this->dispatch("catalog/category/view/foo/bar");
        $fixtureFileName = __DIR__ . DS . "_files" . DS . 'Magento' . DS . 'Catalog' . DS . 'Service'
            . DS . 'TestProduct.php';
        include $fixtureFileName;
        $invoker = Mage::getObjectManager()->create(
            'Magento_Core_Model_DataService_Invoker',
            array('config' => $config)
        );
        /** @var Magento_Core_Model_DataService_Graph $dataServiceGraph */
        $this->_dataServiceGraph = Mage::getObjectManager()->create(
            'Magento_Core_Model_DataService_Graph',
            array('dataServiceInvoker' => $invoker)
        );
    }

    protected function _loadServiceCallsConfig()
    {
        /** @var Magento_Core_Model_Dir $dirs */
        $dirs = Mage::getObjectManager()->create(
            'Magento_Core_Model_Dir', array(
                'baseDir' => array(BP),
                'dirs' => array(Magento_Core_Model_Dir::MODULES => __DIR__ . '/_files'))
        );

        /** @var Magento_Core_Model_Config_Loader_Modules $modulesLoader */
        $modulesLoader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Loader_Modules', array(
                'dirs' => $dirs
            )
        );

        /**
         * Mock is used to disable caching, as far as Integration Tests Framework loads main
         * modules configuration first and it gets cached
         *
         * @var PHPUnit_Framework_MockObject_MockObject $cache
         */
        $cache = $this->getMock('Magento_Core_Model_Config_Cache', array('load', 'save', 'clean', 'getSection'),
            array(), '', false);

        $cache->expects($this->once())
            ->method('load')
            ->will($this->returnValue(false));

        /** @var Magento_Core_Model_Config_Storage $storage */
        $storage = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Storage', array(
                'loader' => $modulesLoader,
                'cache' => $cache
            )
        );

        $config = new Magento_Core_Model_Config_Base('<config />');
        $modulesLoader->load($config);

        /** @var Magento_Core_Model_Config_Modules $modulesConfig */
        $modulesConfig = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Modules', array(
                'storage' => $storage
            )
        );

        /** @var Magento_Core_Model_Config_Loader_Modules_File $fileReader */
        $fileReader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Loader_Modules_File', array(
                'dirs' => $dirs
            )
        );

        /** @var Magento_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = Mage::getObjectManager()->create(
            'Magento_Core_Model_Config_Modules_Reader', array(
                'fileReader' => $fileReader,
                'modulesConfig' => $modulesConfig
            )
        );

        /** @var Magento_Core_Model_DataService_Config_Reader_Factory $dsCfgReaderFactory */
        $dsCfgReaderFactory = Mage::getObjectManager()->create(
            'Magento_Core_Model_DataService_Config_Reader_Factory');

        /** @var Magento_Core_Model_DataService_Config $config */
        $dataServiceConfig = new Magento_Core_Model_DataService_Config($dsCfgReaderFactory, $moduleReader);
        return $dataServiceConfig;
    }

    /**
     * Test Layout initialization of service calls
     */
    public function testServiceCalls()
    {
        /** @var Magento_Core_Model_Layout $layout */
        $layout = $this->_getLayoutModel('layout_update.xml');
        $serviceCalls = $layout->getServiceCalls();
        $expectedServiceCalls = array('testServiceCall' => array(
            'namespaces' => array(
                'block_with_service_calls' => 'testData'
            )
        ));
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
     * @return Magento_Core_Model_Layout
     */
    protected function _getLayoutModel($fixtureFile)
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getObjectManager()
            ->create('Magento_Core_Model_Layout', array('dataServiceGraph' => $this->_dataServiceGraph));
        $xml = simplexml_load_file(__DIR__ . "/_files/{$fixtureFile}", 'Magento_Core_Model_Layout_Element');
        $layout->setXml($xml);
        $layout->generateElements();
        return $layout;
    }
}