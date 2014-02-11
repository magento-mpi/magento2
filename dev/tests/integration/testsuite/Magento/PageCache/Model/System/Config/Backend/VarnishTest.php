<?php
/**
* {license_notice}
*
* @category    Magento
* @package     Magento_PageCache
* @subpackage  integration_tests
* @copyright   {copyright}
* @license     {license_link}
 */
namespace Magento\PageCache\Model\System\Config\Backend;

class VarnishTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\System\Config\Backend\Varnish
     */
    protected $_model;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    protected function setUp()
    {
        $this->_config = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\App\ConfigInterface');
        $data = array(
            'access_list' => 'localhost',
            'backend_host' => 'localhost',
            'backend_port' => 8080,
            'ttl' => 120,
        );
        $this->_config->setValue('system/full_page_cache/default', $data);
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\PageCache\Model\System\Config\Backend\Varnish');
    }

    /**
     * @dataProvider beforeSaveDataProviderForException
     */
    public function testBeforeSaveWithException($value, $path)
    {
        $fieldName = 'field_' . $path;
        $expectedMessage = "Field {$fieldName} not matched with default field";

        $this->_model->setValue($value);
        $this->_model->setPath($path);
        $this->_model->setField($fieldName);
        try {
            $this->_model->save();
        } catch (\Magento\Core\Exception $e) {
            $this->assertEquals($expectedMessage, $e->getMessage());
        }
    }

    public function beforeSaveDataProviderForException()
    {
        return array(
            array('', 'access_list'),
            array('', 'backend_host'),
            array(0, 'backend_port'),
            array(0, 'ttl'),
        );
    }

    /**
     * @dataProvider beforeSaveDataProvider
     *
     * @param $value
     * @param $path
     * @param $expected
     * @param $needUpdate
     */
    public function testBeforeSave($value, $path, $expected, $needUpdate)
    {
        if ($needUpdate) {
            $this->_model->load($path, 'path');
        }

        $this->_model->setValue($value);
        $this->_model->setPath($path);
        $this->_model->setField($path);
        $this->_model->save();
        $value = $this->_model->getValue();

        $this->assertEquals($value, $expected);
    }

    public function beforeSaveDataProvider()
    {
        return array(
            array('localhost', 'access_list', 'localhost', false),
            array('localhost', 'backend_host', 'localhost', false),
            array(8081, 'backend_port', 8081, false),
            array(125, 'ttl', 125, false),

            array('localhost', 'access_list', 'localhost', true),
            array('', 'backend_host', 'localhost', true),
            array(0, 'backend_port', 8080, true),
            array(0, 'ttl', 120, true),
        );
    }
}
