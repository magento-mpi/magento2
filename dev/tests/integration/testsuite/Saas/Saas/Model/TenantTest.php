<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Saas
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Saas_Model_TenantTest extends PHPUnit_Framework_TestCase
{
    public function testGetConfigString()
    {
        $xmlStart = '<?xml version="1.0" encoding="utf-8" ?>';
        $xmlSaasOn = '<modules><Saas><active>true</active></Saas></modules>';
        $xmlLocal = '<global><fast_storage><mongodb>1</mongodb></fast_storage></global>';
        $xmlWrong = '<wrongnode/>';

        $configData = array(
            'local' => $xmlStart . '<config>' . $xmlLocal . '</config>',
            'modules' => $xmlStart . '<config>' . $xmlSaasOn . '</config>',
            'tenantModules' => $xmlStart . '<config>' . $xmlSaasOn . '</config>',
            'wrongNodeName' => $xmlStart . '<config>' . $xmlWrong . '</config>',
        );
        $tenant = new Saas_Saas_Model_Tenant($configData);
        $value = $tenant->getConfigString();
        $this->assertContains($xmlLocal, $value);
        $this->assertContains($xmlSaasOn, $value);
        $this->assertNotContains($xmlWrong, $value);
    }

    /**
     * @dataProvider getModulesDataProvider
     */
    public function testGetTenantModules($configData, $expectedResult)
    {
        $tenant = new Saas_Saas_Model_Tenant($configData);
        $this->assertXmlStringEqualsXmlString($tenant->getTenantModules(), $expectedResult);
    }

    public function getModulesDataProvider()
    {
        $xmlStart = '<?xml version="1.0" encoding="utf-8" ?>';
        $xmlSaasOn = '<config><modules><Saas><active>true</active></Saas></modules></config>';
        $xmlSaasOff = '<config><modules><Saas><active>false</active></Saas></modules></config>';
        $xmlSaas1On = '<config><modules><Saas1><active>true</active></Saas1></modules></config>';
        $xmlEmpty = '<config><modules/></config>';

        return array(
            'empty' => array(
                array('modules' => $xmlStart . $xmlEmpty),
                $xmlEmpty
            ),
            'empty_allowed' => array(
                array('modules' => $xmlStart . $xmlSaasOn),
                $xmlEmpty
            ),
            'non_empty_allowed' => array(
                array(
                    'modules' => $xmlStart . $xmlSaasOn,
                    'tenantModules' => $xmlStart . $xmlSaasOn,
                ),
                $xmlSaasOn
            ),
            'non_empty_allowed_group' => array(
                array(
                    'modules' => $xmlStart . $xmlSaas1On,
                    'tenantModules' => $xmlStart . $xmlSaasOn,
                    'groupModules' => $xmlStart . $xmlSaas1On,
                ),
                $xmlSaas1On
            ),
            'non_empty_allowed_non_active' => array(
                array(
                    'modules' => $xmlStart
                        . '<config>
                            <modules><Saas1><active>true</active></Saas1><Saas><active>true</active></Saas></modules>
                           </config>',
                    'tenantModules' => $xmlStart . $xmlSaasOff,
                ),
                $xmlEmpty
            ),
            'non_empty_allowed_one' => array(
                array(
                    'modules' => $xmlStart
                        . '<config>
                            <modules><Saas1><active>true</active></Saas1><Saas><active>false</active></Saas></modules>
                           </config>',
                    'tenantModules' => $xmlStart . $xmlSaasOn
                ),
                $xmlSaasOff
            ),
            'each_node_has_unique' => array(
                array(
                    'modules' => $xmlStart
                        . '<config>
                            <modules><Saas1><active>true</active></Saas1><Saas><active>false</active></Saas></modules>
                           </config>',
                    'tenantModules' => $xmlStart
                        . '<config>
                            <modules><Saas1><active>true</active></Saas1><Saas2><active>false</active></Saas2></modules>
                           </config>',
                ),
                $xmlSaas1On
            ),
        );
    }

    public function testGetMediaDirGetVarDir()
    {
        $mediaDir = 'mediadir';
        $tenant = new Saas_Saas_Model_Tenant(
            array('local' => '<?xml version="1.0" encoding="utf-8" ?><config><global><web><dir><media>'
                    . $mediaDir . '</media></dir></web></global></config>')
        );
        $this->assertEquals($tenant->getMediaDir(), $mediaDir);
        $this->assertEquals($tenant->getVarDir(), $mediaDir); //yes, there is no specific var dir
    }
}
