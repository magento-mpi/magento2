<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Saas_Model_Tenant_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**#@+
     * Fragments for various fixtures
     */
    const XML_MEDIA_DIR = '<global><web><dir><media>media_dir</media></dir></web></global>';
    const XML_SAAS_ON = '<modules><Saas><active>true</active></Saas></modules>';
    /**#@-*/

    /**
     * @dataProvider constructExceptionDataProvider
     */
    public function testConstructException($configData, $expectedException, $expectedMessage)
    {
        $this->setExpectedException($expectedException, $expectedMessage);
        new Saas_Saas_Model_Tenant_Config(__DIR__, $configData);
    }

    /**
     * @return array
     */
    public function constructExceptionDataProvider()
    {
        return array(
            'no tenantConfiguration' => array(array(), 'InvalidArgumentException', 'Missing key "tenantConfiguration"'),
            'no local'               => array(
                array('tenantConfiguration' => array()),
                'LogicException',
                'Local Configuration does not exist'
            ),
            'no media dir'           => array(
                array('tenantConfiguration' => array('local' => self::_wrapXml(''))),
                'InvalidArgumentException',
                'Media directory name is not set'
            ),
            'no version hash'          => array(
                array('tenantConfiguration' => array('local' => self::_wrapXml(
                    self::XML_MEDIA_DIR
                ))),
                'InvalidArgumentException',
                'Version hash is not specified'
            ),
        );
    }

    public function testGetMediaDirFile()
    {
        $fileName = uniqid();
        $configData = array(
            'tenantConfiguration' => array('local' => self::_wrapXml(self::XML_MEDIA_DIR)),
            'version_hash'          => '1234567',
        );
        $config = new Saas_Saas_Model_Tenant_Config(__DIR__, $configData);
        $result = $config->getMediaDirFile($fileName);
        $this->assertStringStartsWith(__DIR__, $result);
        $this->assertContains('media_dir', $result);
        $this->assertStringEndsWith($fileName, $fileName);
    }

    public function testGetApplicationParameters()
    {
        $configData = array(
            'tenantConfiguration' => array('local' => self::_wrapXml(self::XML_MEDIA_DIR . self::XML_SAAS_ON)),
            'version_hash'        => '1234567',
        );
        $config = new Saas_Saas_Model_Tenant_Config(__DIR__, $configData);
        $result = $config->getApplicationParams();
        $this->assertArrayHasKey(Mage::PARAM_APP_DIRS, $result);
        $this->assertArrayHasKey(Mage_Core_Model_Dir::MEDIA, $result[Mage::PARAM_APP_DIRS]);
        $this->assertContains('media_dir', $result[Mage::PARAM_APP_DIRS][Mage_Core_Model_Dir::MEDIA]);
        $this->assertArrayHasKey(Mage_Core_Model_Dir::VAR_DIR, $result[Mage::PARAM_APP_DIRS]);
        $this->assertContains('media_dir', $result[Mage::PARAM_APP_DIRS][Mage_Core_Model_Dir::VAR_DIR]);
        $this->assertArrayHasKey(Mage::PARAM_APP_URIS, $result);
        $this->assertArrayHasKey(Mage_Core_Model_Dir::MEDIA, $result[Mage::PARAM_APP_URIS]);
        $this->assertContains('media_dir', $result[Mage::PARAM_APP_URIS][Mage_Core_Model_Dir::MEDIA]);
        $this->assertArrayHasKey(Mage::PARAM_CUSTOM_LOCAL_CONFIG, $result);
        $this->assertContains('<Saas>', $result[Mage::PARAM_CUSTOM_LOCAL_CONFIG]);
    }

    /**
     * @dataProvider getModulesDataProvider
     */
    public function testGetTenantModules($fixture, $expectedResult)
    {
        $configData = array(
            'tenantConfiguration' => array(
                'local'         => self::_wrapXml(self::XML_MEDIA_DIR),
                'modules'       => self::_wrapXml(self::XML_MEDIA_DIR . $fixture['modules']),
                'tenantModules' => isset($fixture['tenantModules'])
                    ? self::_wrapXml(self::XML_MEDIA_DIR . $fixture['tenantModules']) : null,
            ),
            'groupConfiguration' => array(
                'modules' => isset($fixture['groupModules'])
                    ? self::_wrapXml(self::XML_MEDIA_DIR . $fixture['groupModules']) : null,
            ),
            'version_hash'        => '1234567',
        );
        $config = new Saas_Saas_Model_Tenant_Config(__DIR__, $configData);
        $result = $config->getApplicationParams();
        $result = $result[Mage::PARAM_CUSTOM_LOCAL_CONFIG];
        $this->assertXmlStringEqualsXmlString(self::_wrapXml(self::XML_MEDIA_DIR . $expectedResult), $result);
    }

    /**
     * @return array
     */
    public function getModulesDataProvider()
    {
        $xmlSaasOn = self::XML_SAAS_ON;
        $xmlSaasOff = '<modules><Saas><active>false</active></Saas></modules>';
        $xmlSaasOneOn = '<modules><Saas1><active>true</active></Saas1></modules>';
        $xmlEmpty = '<modules/>';

        return array(
            'empty' => array(array('modules' => $xmlEmpty), $xmlEmpty),
            'empty_allowed' => array(array('modules' => $xmlSaasOn), $xmlEmpty),
            'non_empty_allowed' => array(array('modules' => $xmlSaasOn, 'tenantModules' => $xmlSaasOn,), $xmlSaasOn),
            'non_empty_allowed_group' => array(
                array(
                    'modules' => $xmlSaasOneOn,
                    'tenantModules' => $xmlSaasOn,
                    'groupModules' => $xmlSaasOneOn,
                ),
                $xmlSaasOneOn
            ),
            'non_empty_allowed_non_active' => array(
                array(
                    'modules' =>
                        '<modules><Saas1><active>true</active></Saas1><Saas><active>true</active></Saas></modules>',
                    'tenantModules' => $xmlSaasOff,
                ),
                $xmlEmpty
            ),
            'non_empty_allowed_one' => array(
                array(
                    'modules' =>
                        '<modules><Saas1><active>true</active></Saas1><Saas><active>false</active></Saas></modules>',
                    'tenantModules' => $xmlSaasOn
                ),
                $xmlSaasOff
            ),
            'each_node_has_unique' => array(
                array(
                    'modules' =>
                        '<modules><Saas1><active>true</active></Saas1><Saas><active>false</active></Saas></modules>',
                    'tenantModules' =>
                        '<modules><Saas1><active>true</active></Saas1><Saas2><active>false</active></Saas2></modules>',
                ),
                $xmlSaasOneOn
            ),
        );
    }

    /**
     * Shortcut for wrapping XML-fixture into a well-formed XML-document
     *
     * @param string $contents
     * @return string
     */
    private static function _wrapXml($contents)
    {
        return '<?xml version="1.0" encoding="UTF-8"?><config>' . $contents . '</config>';
    }

    /**
     * @dataProvider loadModulesDataProvider
     */
    public function testLoadModulesFromString($modulesString, $expectedResult)
    {
        $method = new ReflectionMethod('Saas_Saas_Model_Tenant_Config', '_loadModulesFromString');
        $method->setAccessible(true);
        $result = $method->invoke(null, $modulesString);
        $this->assertInternalType('array', $result);
        $this->assertEquals($result, $expectedResult);
    }

    /**
     * @return array
     */
    public function loadModulesDataProvider()
    {
        return array(
            'empty_string' => array(
                '',
                array()
            ),
            'non_empty_string' => array(
                '<?xml version="1.0" encoding="utf-8" ?><config><modules>'
                    . '<Test_Module><active>true</active></Test_Module>'
                    . '<Test_Module1><active>false</active></Test_Module1>'
                    . '</modules></config>',
                array('Test_Module' => array('active' => 'true'), 'Test_Module1' => array('active' => 'false'))
            ),
        );
    }
}
