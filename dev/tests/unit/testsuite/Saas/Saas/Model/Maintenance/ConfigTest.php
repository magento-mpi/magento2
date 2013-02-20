<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Saas_Model_Maintenance_ConfigTest extends PHPUnit_Framework_TestCase
{
    public function testIsMaintenanceModeDisabled()
    {
        $params = null;
        $model = new Saas_Saas_Model_Maintenance_Config($params);
        $this->assertFalse($model->isMaintenanceMode());
    }

    public function testIsMaintenanceModeEnabled()
    {
        $params = array(
            'enable' => 1,
        );
        $model = new Saas_Saas_Model_Maintenance_Config($params);
        $this->assertTrue($model->isMaintenanceMode());
    }

    public function testGetWhiteListEmpty()
    {
        $params = array();
        $model = new Saas_Saas_Model_Maintenance_Config($params);
        $this->assertEquals(array(), $model->getWhiteList());
    }

    public function testGetWhiteListNotEmpty()
    {
        $params = array('whitelist' => '127.0.0.1, 127.0.0.2');
        $model = new Saas_Saas_Model_Maintenance_Config($params);
        $expected = array('127.0.0.1', '127.0.0.2');
        $this->assertEquals($expected, $model->getWhiteList());
    }

    public function testGeUrlEmpty()
    {
        $params = array();
        $model = new Saas_Saas_Model_Maintenance_Config($params);
        $this->assertNull($model->getUrl());
    }

    public function testGeUrlNotEmpty()
    {
        $params = array('url' => 'http://some/url');
        $model = new Saas_Saas_Model_Maintenance_Config($params);
        $this->assertEquals('http://some/url', $model->getUrl());
    }
}
