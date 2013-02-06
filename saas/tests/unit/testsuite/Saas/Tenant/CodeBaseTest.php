<?php
/**
 * Unit test for Saas_Tenant_CodeBase
 */
class Saas_Tenant_CodeBaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    private $_id;

    /**
     * @var array
     */
    private static $_config = array(
        'tenantConfiguration' => array('local' => '<?xml version="1.0"?><config/>'),
        'version' => '1.0.0.0',
        'maintenanceMode' => false,
    );

    protected function setUp()
    {
        $this->_id = uniqid();
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Directory does not exist: 'invalid_dir'
     */
    public function testConstructException()
    {
        new Saas_Tenant_CodeBase('identifier', 'invalid_dir', array());
    }

    public function testGetId()
    {
        $object = new Saas_Tenant_CodeBase($this->_id, __DIR__, self::$_config);
        $this->assertEquals($this->_id, $object->getId());
    }

    public function testGetVersion()
    {
        $config = self::$_config;
        $config['version'] = uniqid();
        $object = new Saas_Tenant_CodeBase($this->_id, __DIR__, $config);
        $this->assertEquals($config['version'], $object->getVersion());
    }

    /**
     * @param bool $value
     * @dataProvider isUserUnderMaintenanceDataProvider
     */
    public function testIsUserUnderMaintenance($value)
    {
        $config = self::$_config;
        $config['maintenanceMode'] = $value;
        $object = new Saas_Tenant_CodeBase($this->_id, __DIR__, $config);
        $this->assertEquals($value, $object->isUnderMaintenance());
    }

    /**
     * @return array
     */
    public function isUserUnderMaintenanceDataProvider()
    {
        return array(
            array(true),
            array(1),
            array(false),
            array(0),
        );
    }

    public function testGetDir()
    {
        $config = self::$_config;
        $config['version'] = '2.0.0.0';
        $object = new Saas_Tenant_CodeBase($this->_id, __DIR__ . DIRECTORY_SEPARATOR . '_files', $config);
        $expected = __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . '2.0.0.0';
        $this->assertEquals($expected, $object->getDir());

        $msg = "Directory does not exist: '" . __DIR__ . DIRECTORY_SEPARATOR . "2.0.0.0'";
        $this->setExpectedException('LogicException', $msg);
        $object = new Saas_Tenant_CodeBase($this->_id, __DIR__, $config);
        $object->getDir();
    }

    public function testGetMediaDirName()
    {
        $object = new Saas_Tenant_CodeBase($this->_id, __DIR__, self::$_config);
        $this->assertEquals('media' . DIRECTORY_SEPARATOR . $this->_id, $object->getMediaDirName());

        $config = self::$_config;
        $config['tenantConfiguration']['local'] = '<?xml version="1.0"?><config><global><web><dir><media>' . 'test'
            . '</media></dir></web></global></config>';
        $object = new Saas_Tenant_CodeBase($this->_id, __DIR__, $config);
        $this->assertEquals('media' . DIRECTORY_SEPARATOR . 'test', $object->getMediaDirName());
    }
}
