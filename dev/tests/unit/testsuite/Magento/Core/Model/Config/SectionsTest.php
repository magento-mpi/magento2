<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_SectionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Config_Sections
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Magento_Core_Model_Config_Sections();
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param string $path
     * @param string|bool $expectedResult
     * @dataProvider getKeyDataProvider
     */
    public function testGetKey($path, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->_model->getKey($path));
    }

    public function getKeyDataProvider()
    {
        return array(
            array('admin/user/active', 'admin'),
            array('adminhtml/routers/default', 'adminhtml'),
            array('crontab/routers/default', 'crontab'),
            array('install/routers/default', 'install'),
            array('stores/admin/routers/default', 'stores_admin'),
            array('stores/default/routers/default', 'stores_default'),
            array('stores/custom/routers/default', 'stores_custom'),
            array('websites/custom/routers/default', 'websites'),
            array('global/custom/routers/default', false),
        );
    }
}
