<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Config;

class DataTest extends \PHPUnit_Framework_TestCase
{
    const SAMPLE_CONFIG_PATH = 'web/unsecure/base_url';
    const SAMPLE_VALUE = 'http://example.com/';

    /**
     * @var \Magento\Core\Model\Config\Value
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * Set Up
     */
    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->_model = $this->objectManager->create('Magento\Core\Model\Config\Value');

    }

    public static function setUpBeforeClass()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config\Storage\Db')
            ->save(self::SAMPLE_CONFIG_PATH, self::SAMPLE_VALUE);
        self::_refreshConfiguration();
    }

    public static function tearDownAfterClass()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\Config\Storage\Db')
            ->delete(self::SAMPLE_CONFIG_PATH);
        self::_refreshConfiguration();
    }

    /**
     * Remove cached configuration and reinitialize the application
     */
    protected static function _refreshConfiguration()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
            ->cleanCache(array(\Magento\App\Config::CACHE_TAG));
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize();
    }

    public function testIsValueChanged()
    {
        /** @var \Magento\Core\Model\Config\Value $configValue */
        $configValue = $this->objectManager->create('Magento\Core\Model\Config\Value');
        $configValue->load('value-for-test-testIsValueChanged', 'path');
        $configValue->setScope('default')
            ->setScopeId(0)
            ->setId($configValue->getId())
            ->setValue('original-value')
            ->setPath('value-for-test-testIsValueChanged')
            ->save();
            $this->assertNotEmpty($configValue->getId());
            $this->assertFalse($configValue->isValueChanged());
            $configValue->setValue('new-value');
            $this->assertTrue($configValue->isValueChanged());
    }

    public function testGetOldValue()
    {
        $this->_model->setPath(self::SAMPLE_CONFIG_PATH);
        $this->assertEquals(self::SAMPLE_VALUE, $this->_model->getOldValue());

        $this->_model->setWebsiteCode('base');
        $this->assertEquals(self::SAMPLE_VALUE, $this->_model->getOldValue());

        $this->_model->setStoreCode('default');
        $this->assertEquals(self::SAMPLE_VALUE, $this->_model->getOldValue());
    }

    public function testGetFieldsetDataValue()
    {
        $this->assertNull($this->_model->getFieldsetDataValue('key'));
        $this->_model->setFieldsetData(array('key' => 'value'));
        $this->assertEquals('value', $this->_model->getFieldsetDataValue('key'));
    }

    public function testCRUD()
    {
        $this->_model->setData(
            array(
                'scope'     => 'default',
                'scope_id'  => 0,
                'path'      => 'test/config/path',
                'value'     => 'test value'
            )
        );
        $crud = new \Magento\TestFramework\Entity($this->_model, array('value' => 'new value'));
        $crud->testCrud();
    }

    public function testCollection()
    {
        $collection = $this->_model->getCollection();
        $collection->addScopeFilter('test', 0, 'test')
            ->addPathFilter('not_existing_path')
            ->addValueFilter('not_existing_value');
        $this->assertEmpty($collection->getItems());
    }
}
