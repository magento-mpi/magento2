<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Resource\Config'
        );
    }

    public function testSaveDeleteConfig()
    {
        $connection = $this->_model->getReadConnection();
        $select = $connection->select()->from($this->_model->getMainTable())->where('path=?', 'test/config');
        $this->_model->saveConfig('test/config', 'test', 'default', 0);
        $this->assertNotEmpty($connection->fetchRow($select));

        $this->_model->deleteConfig('test/config', 'default', 0);
        $this->assertEmpty($connection->fetchRow($select));
    }
}
