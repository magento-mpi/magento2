<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Db_UpdaterTest extends PHPUnit_Framework_TestCase
{
    protected $_xmlData;

    /** @var Magento_Core_Model_Config|PHPUnit_Framework_MockObject_MockObject */
    protected $_config;

    /** @var Magento_Core_Model_Resource_Setup|PHPUnit_Framework_MockObject_MockObject */
    protected $_resourceSetup;

    /** @var Magento_Core_Model_Resource_SetupFactory|PHPUnit_Framework_MockObject_MockObject */
    protected $_factory;

    /** @var Magento_Core_Model_App_State|PHPUnit_Framework_MockObject_MockObject */
    protected $_app;

    /**
     * Initialize required data
     */
    protected function setUp()
    {
        $this->_xmlData =
        "<?xml version=\"1.0\"?>
        <config>
            <global>
                <resources>
                    <fixture_module_setup>
                        <setup>
                            <class>Magento_Core_Model_Resource_Setup</class>
                        </setup>
                    </fixture_module_setup>
                </resources>
            </global>
        </config>";
        $this->_config = $this->getMock('Magento_Core_Model_Config', array(), array(), '', false);
        $this->_resourceSetup = $this->getMock('Magento_Core_Model_Resource_Setup', array(), array(), '', false);
        $this->_factory = $this->getMock('Magento_Core_Model_Resource_SetupFactory', array(), array(), '', false);
        $this->_app = $this->getMock('Magento_Core_Model_App_State', array(), array(), '', false);
    }

    /**
     * Test case with running update scripts
     */
    public function testUpdateScheme()
    {
        $configElement = new Magento_Core_Model_Config_Element($this->_xmlData);
        $configuration = new Magento_Simplexml_Config($configElement);

        $this->_config->expects($this->any())
            ->method('getNode')
            ->will($this->returnValue($configuration->getNode('global/resources')));

        $this->_resourceSetup->expects($this->once())
            ->method('applyUpdates');
        $this->_resourceSetup->expects($this->once())
            ->method('getCallAfterApplyAllUpdates')
            ->will($this->returnValue(true));
        $this->_resourceSetup->expects($this->once())
            ->method('afterApplyAllUpdates');

        $this->_factory->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento_Core_Model_Resource_Setup'),
                $this->equalTo(array('resourceName' => 'fixture_module_setup'))
            )
            ->will($this->returnValue($this->_resourceSetup));

        $this->_app->expects($this->at(0))
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $this->_app->expects($this->at(1))
            ->method('setUpdateMode')
            ->with($this->equalTo(true));
        $this->_app->expects($this->at(2))
            ->method('setUpdateMode')
            ->with($this->equalTo(false));

        $updater = new Magento_Core_Model_Db_Updater(
            $this->_config,
            $this->_factory,
            $this->_app,
            false
        );

        $updater->updateScheme();
    }

    /**
     * Skip update scripts
     */
    public function testUpdateSchemaSkip()
    {
        $this->_app->expects($this->at(0))
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $this->_config->expects($this->never())
            ->method('getNode');

        $updater = new Magento_Core_Model_Db_Updater(
            $this->_config,
            $this->_factory,
            $this->_app,
            true
        );
        $updater->updateScheme();
    }

    /**
     * Update schema and update data
     */
    public function testUpdateData()
    {
        $xml =
            "<?xml version=\"1.0\"?>
            <config>
                <global>
                    <resources>
                        <fixture_module_setup>
                        </fixture_module_setup>
                    </resources>
                </global>
            </config>";
        $configElement = new Magento_Core_Model_Config_Element($xml);
        $configuration = new Magento_Simplexml_Config($configElement);

        $this->_config->expects($this->at(0))
            ->method('getNode')
            ->with($this->equalTo('global/resources'))
            ->will($this->returnValue($configuration->getNode('global/resources')));

        $this->_app->expects($this->at(0))
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $this->_app->expects($this->at(1))
            ->method('setUpdateMode');

        $updater = new Magento_Core_Model_Db_Updater(
            $this->_config,
            $this->_factory,
            $this->_app,
            false
        );

        $updater->updateScheme();

        $configElement = new Magento_Core_Model_Config_Element($this->_xmlData);
        $configuration = new Magento_Simplexml_Config($configElement);

        $this->_config->expects($this->at(0))
            ->method('getNode')
            ->with($this->equalTo('global/resources'))
            ->will($this->returnValue($configuration->getNode('global/resources')));

        $this->_factory->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('Magento_Core_Model_Resource_Setup'),
                $this->equalTo(array('resourceName' => 'fixture_module_setup'))
            )
            ->will($this->returnValue($this->_resourceSetup));
        $this->_resourceSetup->expects($this->once())
            ->method('applyDataUpdates');

        $updater->updateData();
    }

    /**
     * Not update data
     */
    public function testUpdateDataNotUpdated()
    {
        $updater = new Magento_Core_Model_Db_Updater(
            $this->_config,
            $this->_factory,
            $this->_app,
            false
        );
        $this->_config->expects($this->never())
            ->method('getNode');

        $updater->updateData();
    }
}
