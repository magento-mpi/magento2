<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Backend_Model_Config_Backend_DomainsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Default Magento Go domain
     */
    const DEF_DOMAIN = 'default.domain.com';

    /**
     * Custom tenant domain
     */
    const CUST_DOMAIN = 'customdomain.com';

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_configMock;

    /**
     * @var Mage_Core_Model_Context
     */
    protected $_contextMock;

    /**
     * @var Mage_Core_Model_Config_Storage_Writer_Db
     */
    protected $_writerMock;

    /**
     * Resource mock for application
     *
     * @var Mage_Core_Model_Resource_Abstract
     */
    protected $_resourceMock;

    /**
     * Database collection mock
     *
     * @var Varien_Data_Collection_Db
     */
    protected $_dbMock;

    /**
     * Prepare mock objects for test
     */
    public function setUp()
    {
        $this->_configMock = $this->getMock('Mage_Core_Model_Config', array(), array(), '', false);
        $this->_contextMock = $this->getMock('Mage_Core_Model_Context', array(), array(), '', false);
        $this->_writerMock = $this->getMock('Mage_Core_Model_Config_Storage_Writer_Db', array(), array(), '', false);
        $this->_dbMock = $this->getMock('Varien_Data_Collection_Db', array(), array(), '', false);
        $this->_resourceMock = $this->getMock('Mage_Core_Model_Resource_Config', array(), array(), '', false);
    }

    /**
     * Dataprovider for afterCommitCallback
     *
     * @return array
     */
    public function afterCommitCallbackDataProvider()
    {
        return array(
            array(
                array(
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_DEFAULT_DOMAIN, '', null, self::DEF_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_DOMAIN, '', null, ''),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_SSL, '', null, ''),
                ),
                array(
                    'active_domain' => array(
                        'fields' => array(
                            'active_domain' => array(
                                'value' => 'http://' . self::DEF_DOMAIN . '/',
                            ),
                        ),
                    ),
                ),
                $this->getValuesThatShouldBeSaved(self::DEF_DOMAIN, self::DEF_DOMAIN, self::DEF_DOMAIN),
            ),
            array(
                array(
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_DEFAULT_DOMAIN, '', null, self::DEF_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_DOMAIN, '', null, self::CUST_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_SSL, '', null, 0),
                ),
                array(
                    'active_domain' => array(
                        'fields' => array(
                            'active_domain' => array(
                                'value' => 'http://' . self::CUST_DOMAIN . '/',
                            ),
                        ),
                    ),
                ),
                $this->getValuesThatShouldBeSaved(self::DEF_DOMAIN, self::CUST_DOMAIN, self::DEF_DOMAIN),
            ),
            array(
                array(
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_DEFAULT_DOMAIN, '', null, self::DEF_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_DOMAIN, '', null, self::CUST_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_SSL, '', null, 1),
                ),
                array(
                    'active_domain' => array(
                        'fields' => array(
                            'active_domain' => array(
                                'value' => 'http://' . self::CUST_DOMAIN . '/',
                            ),
                        ),
                    ),
                ),
                $this->getValuesThatShouldBeSaved(self::DEF_DOMAIN, self::CUST_DOMAIN, self::CUST_DOMAIN),
            ),
            array(
                array(
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_DEFAULT_DOMAIN, '', null, self::DEF_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_DOMAIN, '', null, self::CUST_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_SSL, '', null, 1),
                ),
                array(
                    'active_domain' => array(
                        'fields' => array(
                            'active_domain' => array(
                                'value' => 'http://' . self::DEF_DOMAIN . '/',
                            ),
                        ),
                    ),
                ),
                $this->getValuesThatShouldBeSaved(self::DEF_DOMAIN, self::DEF_DOMAIN, self::DEF_DOMAIN),
            ),
        );
    }

    /**
     * @param $defaultDomain
     * @param $customDomain
     * @param $customSslDomain
     *
     * @return array
     */
    public function getValuesThatShouldBeSaved($defaultDomain, $customDomain, $customSslDomain)
    {
        $values = array();
        $key = Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL
            . Saas_Backend_Model_Config_Backend_Domains::HTTPS . '://' . $defaultDomain
            . Mage_Core_Model_Config::SCOPE_DEFAULT;
        $values[$key] = 1;

        $key = Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL
            . Saas_Backend_Model_Config_Backend_Domains::HTTPS . '://' . $defaultDomain
            . Mage_Core_Model_Config::SCOPE_DEFAULT;
        $values[$key] = 1;

        $key = Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL
            . Saas_Backend_Model_Config_Backend_Domains::HTTP . '://' . $defaultDomain
            . Mage_Core_Model_Config::SCOPE_DEFAULT;
        $values[$key] = 1;

        $key = Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL
            . Saas_Backend_Model_Config_Backend_Domains::HTTP . '://' . $defaultDomain
            . Mage_Core_Model_Config::SCOPE_DEFAULT;
        $values[$key] = 1;

        $key = Mage_Core_Model_Store::XML_PATH_SECURE_BASE_LINK_URL
            . Saas_Backend_Model_Config_Backend_Domains::HTTPS . '://' . $customSslDomain
            . Mage_Core_Model_Config::SCOPE_WEBSITES;
        $values[$key] = 1;

        $key = Mage_Core_Model_Store::XML_PATH_SECURE_BASE_URL
            . Saas_Backend_Model_Config_Backend_Domains::HTTPS . '://' . $customSslDomain
            . Mage_Core_Model_Config::SCOPE_WEBSITES;
        $values[$key] = 1;

        $key = Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_LINK_URL
            . Saas_Backend_Model_Config_Backend_Domains::HTTP . '://' . $customDomain
            . Mage_Core_Model_Config::SCOPE_WEBSITES;
        $values[$key] = 1;

        $key = Mage_Core_Model_Store::XML_PATH_UNSECURE_BASE_URL
            . Saas_Backend_Model_Config_Backend_Domains::HTTP . '://' . $customDomain
            . Mage_Core_Model_Config::SCOPE_WEBSITES;
        $values[$key] = 1;

        return $values;
    }

    /**
     * Current active domain changes validation
     *
     * @param array $map
     * @param array $data
     * @param array $shouldBeSaved
     *
     * @test
     * @dataProvider afterCommitCallbackDataProvider
     */
    public function afterCommitCallback(array $map, array $data, array $shouldBeSaved)
    {
        $savedValues   = array();
        $this->_configMock->expects($this->any())->method('getNode')->will($this->returnValueMap($map));
        $this->_configMock->expects($this->once())->method('reinit');
        $this->_writerMock->expects($this->any())->method('save')
            ->will(
                $this->returnCallback(
                    function($path, $value, $scope) use (& $savedValues){
                        $savedValues[($path . $value . $scope)] = 1;
                        return $savedValues;
                    }
                )
            );
        $domainsModel = new Saas_Backend_Model_Config_Backend_Domains(
            $this->_configMock,
            $this->_writerMock,
            $this->_contextMock,
            $this->_resourceMock,
            $this->_dbMock
        );

        $domainsModel->setData('groups', $data);
        $domainsModel->afterCommitCallback();
        $this->assertEmpty(
            array_diff_key($shouldBeSaved, $savedValues),
            'Not all values were saved into `core_config_data`'
        );
    }

    /**
     * Data provider for getAvailableDomains function
     *
     * @return array
     */
    public function getAvailableDomainsDataProvider()
    {
        return array(
            array(
                array(
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_DEFAULT_DOMAIN, '', null, self::DEF_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_DOMAIN, '', null, ''),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_SSL, '', null, ''),
                ),
                array(
                    self::DEF_DOMAIN => self::DEF_DOMAIN,
                ),
            ),
            array(
                array(
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_DEFAULT_DOMAIN, '', null, self::DEF_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_DOMAIN, '', null, self::CUST_DOMAIN),
                    array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_SSL, '', null, ''),
                ),
                array(
                    self::DEF_DOMAIN => self::DEF_DOMAIN,
                    self::CUST_DOMAIN => self::CUST_DOMAIN,
                ),
            ),
        );
    }

    /**
     * Test getting of available domains list
     *
     * @test
     * @dataProvider getAvailableDomainsDataProvider
     */
    public function getAvailableDomains($map, $expectedArray)
    {
        $this->_configMock->expects($this->any())->method('getNode')->will($this->returnValueMap($map));

        /** @var  Saas_Backend_Model_Config_Backend_Domains $domainsModel */
        $domainsModel = new Saas_Backend_Model_Config_Backend_Domains(
            $this->_configMock,
            $this->_writerMock,
            $this->_contextMock,
            $this->_resourceMock,
            $this->_dbMock
        );

        $this->assertEquals($expectedArray, $domainsModel->getAvailableDomains());
    }

    /**
     * Check is we extract valid default domain from config
     *
     * @test
     */
    public function getDefaultDomain()
    {
        $map = array(
            array(Saas_Backend_Model_Config_Backend_Domains::XML_DEFAULT_DOMAIN, '', null, self::DEF_DOMAIN),
            array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_DOMAIN, '', null, self::CUST_DOMAIN),
            array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_SSL, '', null, 1),
        );
        $this->_configMock->expects($this->any())->method('getNode')->will($this->returnValueMap($map));

        /** @var  Saas_Backend_Model_Config_Backend_Domains $domainsModel */
        $domainsModel = new Saas_Backend_Model_Config_Backend_Domains(
            $this->_configMock,
            $this->_writerMock,
            $this->_contextMock,
            $this->_resourceMock,
            $this->_dbMock
        );

        $this->assertEquals(
            $this->_configMock->getNode(Saas_Backend_Model_Config_Backend_Domains::XML_DEFAULT_DOMAIN),
            $domainsModel->getDefaultDomain()
        );
    }

    /**
     * Check is we extract valid default domain from config
     *
     * @test
     */
    public function getCustomDomain()
    {
        $map = array(
            array(Saas_Backend_Model_Config_Backend_Domains::XML_DEFAULT_DOMAIN, '', null, self::DEF_DOMAIN),
            array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_DOMAIN, '', null, self::CUST_DOMAIN),
            array(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_SSL, '', null, 1),
        );
        $this->_configMock->expects($this->any())->method('getNode')->will($this->returnValueMap($map));

        /** @var  Saas_Backend_Model_Config_Backend_Domains $domainsModel */
        $domainsModel = new Saas_Backend_Model_Config_Backend_Domains(
            $this->_configMock,
            $this->_writerMock,
            $this->_contextMock,
            $this->_resourceMock,
            $this->_dbMock
        );

        $this->assertEquals(
            $this->_configMock->getNode(Saas_Backend_Model_Config_Backend_Domains::XML_CUSTOM_DOMAIN),
            $domainsModel->getCustomDomain()
        );
    }
}