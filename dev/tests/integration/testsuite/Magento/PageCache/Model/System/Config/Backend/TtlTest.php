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

class TtlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Model\System\Config\Backend\Ttl
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
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\PageCache\Model\System\Config\Backend\Ttl');
    }

    /**
     * @dataProvider beforeSaveDataProvider
     *
     * @param $value
     * @param $path
     */
    public function testBeforeSave($value, $path)
    {
        $this->_prepareData($value, $path);
    }

    public function beforeSaveDataProvider()
    {
        return array(
            array(125, 'ttl_1'),
            array(0, 'ttl_2'),
        );
    }

    /**
     * @dataProvider beforeSaveDataProviderWithException
     *
     * @param $value
     * @param $path
     */
    public function testBeforeSaveWithException($value, $path)
    {
        $this->setExpectedException('\Magento\Core\Exception');
        $this->_prepareData($value, $path);
    }

    public function beforeSaveDataProviderWithException()
    {
        return array(
            array('', 'ttl_3'),
            array('sdfg', 'ttl_4')
        );
    }

    /**
     * @param $value
     * @param $path
     */
    protected function _prepareData($value, $path)
    {
        $this->_model->setValue($value);
        $this->_model->setPath($path);
        $this->_model->setField($path);
        $this->_model->save();
    }
}
