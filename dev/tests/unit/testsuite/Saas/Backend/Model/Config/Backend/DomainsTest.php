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
                                'value' => self::DEF_DOMAIN,
                            ),
                        ),
                    ),
                ),
                self::DEF_DOMAIN,
                self::DEF_DOMAIN,
                self::DEF_DOMAIN,
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
                                'value' => self::CUST_DOMAIN,
                            ),
                        ),
                    ),
                ),
                self::DEF_DOMAIN,
                self::CUST_DOMAIN,
                self::DEF_DOMAIN,
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
                                'value' => self::CUST_DOMAIN,
                            ),
                        ),
                    ),
                ),
                self::DEF_DOMAIN,
                self::CUST_DOMAIN,
                self::CUST_DOMAIN,
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
                                'value' => self::DEF_DOMAIN,
                            ),
                        ),
                    ),
                ),
                self::DEF_DOMAIN,
                self::DEF_DOMAIN,
                self::DEF_DOMAIN,
            ),
        );
    }

    /**
     * Current active domain changes validation
     *
     * @test
     * @dataProvider afterCommitCallbackDataProvider
     */
    public function afterCommitCallback($map, $data,  $defaultDomain, $customDomain, $customSslDomain)
    {
        $this->_configMock->expects($this->any())->method('getNode')->will($this->returnValueMap($map));

        /** @var  Saas_Backend_Model_Config_Backend_Domains $domainsModel */
        $domainsModel = $this->_writerMock = $this->getMock(
            'Saas_Backend_Model_Config_Backend_Domains',
            array(
                'saveDefaultSecureDomain',
                'saveDefaultUnsecureDomain',
                'saveWebsitesSecureDomain',
                'saveWebsitesUnsecureDomain',
            ),
            array(
                $this->_configMock,
                $this->_writerMock,
                $this->_contextMock,
                $this->_resourceMock,
                $this->_dbMock,
            )
        );
        $domainsModel->setData('groups', $data);

        $domainsModel->expects($this->once())->method('saveDefaultSecureDomain')->with($this->equalTo($defaultDomain));
        $domainsModel->expects($this->once())
            ->method('saveDefaultUnsecureDomain')->with($this->equalTo($defaultDomain));
        $domainsModel->expects($this->once())
            ->method('saveWebsitesSecureDomain')->with($this->equalTo($customSslDomain));
        $domainsModel->expects($this->once())
            ->method('saveWebsitesUnsecureDomain')->with($this->equalTo($customDomain));

        $domainsModel->afterCommitCallback();
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