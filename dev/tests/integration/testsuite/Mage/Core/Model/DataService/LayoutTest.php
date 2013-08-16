<?php
/**
 * Set of tests of layout directives handling behavior
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_DataService_LayoutTest extends Magento_Test_TestCase_ControllerAbstract
{
    private $_dataServiceGraph;

    public function setUp()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        // Need to call this first so we get proper config
        $config = $this->_loadServiceCallsConfig();
        parent::setUp();
        $this->dispatch("catalog/category/view/foo/bar");
        $fixtureFileName = __DIR__ . DS . "_files" . DS . 'Mage' . DS . 'Catalog' . DS . 'Service'
            . DS . 'TestProduct.php';
        include $fixtureFileName;
        $invoker = $objectManager->create(
            'Mage_Core_Model_DataService_Invoker',
            array('config' => $config)
        );
        /** @var Mage_Core_Model_DataService_Graph $dataServiceGraph */
        $this->_dataServiceGraph = $objectManager->create(
            'Mage_Core_Model_DataService_Graph',
            array('dataServiceInvoker' => $invoker)
        );
    }

    protected function _loadServiceCallsConfig()
    {
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        /** @var Mage_Core_Model_Dir $dirs */
        $dirs = $objectManager->create(
            'Mage_Core_Model_Dir', array(
                'baseDir' => array(BP),
                'dirs' => array(Mage_Core_Model_Dir::MODULES => __DIR__ . '/_files'))
        );

        /** @var Mage_Core_Model_Config_Modules_Reader $moduleReader */
        $moduleReader = $objectManager->create(
            'Mage_Core_Model_Config_Modules_Reader', array(
                'dirs' => $dirs,
            )
        );

        /** @var Mage_Core_Model_DataService_Config_Reader_Factory $dsCfgReaderFactory */
        $dsCfgReaderFactory = $objectManager->create(
            'Mage_Core_Model_DataService_Config_Reader_Factory');

        /** @var Mage_Core_Model_DataService_Config $config */
        $dataServiceConfig = new Mage_Core_Model_DataService_Config($dsCfgReaderFactory, $moduleReader);
        return $dataServiceConfig;
    }

    /**
     * Test Layout initialization of service calls
     */
    public function testServiceCalls()
    {
        /** @var Mage_Core_Model_Layout $layout */
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
     * @return Mage_Core_Model_Layout
     */
    protected function _getLayoutModel($fixtureFile)
    {
        /** @var $layout Mage_Core_Model_Layout */
        $layout = Magento_Test_Helper_Bootstrap::getObjectManager()
            ->create('Mage_Core_Model_Layout', array('dataServiceGraph' => $this->_dataServiceGraph));
        $xml = simplexml_load_file(__DIR__ . "/_files/{$fixtureFile}", 'Mage_Core_Model_Layout_Element');
        $layout->setXml($xml);
        $layout->generateElements();
        return $layout;
    }
}
