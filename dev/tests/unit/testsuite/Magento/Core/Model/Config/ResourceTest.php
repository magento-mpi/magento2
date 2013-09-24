<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config;

class ResourceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Resource
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configMock;

    protected function setUp()
    {
        $this->_configMock = new \Magento\Core\Model\Config\Base('
        <config>
            <global>
                <resources>
                    <default_setup>
                        <connection>
                            <type>pdo_mysql</type>
                            <model>mysql4</model>
                        </connection>
                    </default_setup>
                    <default_read>
                        <connection>
                            <use>default_setup</use>
                        </connection>
                    </default_read>
                    <core_setup>
                        <connection>
                            <use>default_setup</use>
                        </connection>
                    </core_setup>
                    <db>
                        <table_prefix>some_prefix_</table_prefix>
                    </db>
                </resources>
                <resource>
                    <connection>
                        <types>
                            <pdo_mysql>Mysql_Config</pdo_mysql>
                        </types>
                    </connection>
                </resource>
            </global>
        </config>
        ');
        $this->_model = new \Magento\Core\Model\Config\Resource($this->_configMock);
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_configMock);
    }

    public function testGetResourceConfig()
    {
        $resourceConfig = $this->_model->getResourceConfig('default_read');
        $this->assertEquals('default_setup', (string) $resourceConfig->connection->use);
    }

    public function testGetResourceConnectionConfig()
    {
        $resourceConfig = $this->_model->getResourceConnectionConfig('default_setup');
        $this->assertEquals('pdo_mysql', (string) $resourceConfig->type);
        $this->assertEquals('mysql4', (string) $resourceConfig->model);
    }

    public function testGetResourceConnectionConfigUsesInheritance()
    {
        $resourceConfig = $this->_model->getResourceConnectionConfig('default_read');
        $this->assertEquals('pdo_mysql', (string) $resourceConfig->type);
        $this->assertEquals('mysql4', (string) $resourceConfig->model);
    }

    public function testGetTablePrefix()
    {
        $this->assertEquals('some_prefix_', $this->_model->getTablePrefix());
    }

    public function testGetResourceTypeConfig()
    {
        $this->assertEquals('Mysql_Config', $this->_model->getResourceTypeConfig('pdo_mysql'));
    }

    public function testGetResourceConnectionModel()
    {
        $this->assertEquals('mysql4', $this->_model->getResourceConnectionModel('core'));
    }
}
