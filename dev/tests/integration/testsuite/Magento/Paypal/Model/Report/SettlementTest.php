<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Paypal_Model_Report_SettlementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testFetchAndSave()
    {
        /** @var $model \Magento\Paypal\Model\Report\Settlement; */
        $model = Mage::getModel('Magento\Paypal\Model\Report\Settlement');
        $connection = $this->getMock('Magento\Io\Sftp', array('rawls', 'read'), array(), '', false);
        $filename = 'STL-00000000.00.abc.CSV';
        $connection->expects($this->once())->method('rawls')->will($this->returnValue(array($filename => array())));
        $connection->expects($this->once())->method('read')->with($filename, $this->anything());
        $model->fetchAndSave($connection);
    }

    /**
     * @param array $config
     * @expectedException InvalidArgumentException
     * @dataProvider createConnectionExceptionDataProvider
     */
    public function testCreateConnectionException($config)
    {
        \Magento\Paypal\Model\Report\Settlement::createConnection($config);
    }

    /**
     * @return array
     */
    public function createConnectionExceptionDataProvider()
    {
        return array(
            array(array()),
            array(array('username' => 'test', 'password' => 'test', 'path' => '/')),
            array(array('hostname' => 'example.com', 'password' => 'test', 'path' => '/')),
            array(array('hostname' => 'example.com', 'username' => 'test', 'path' => '/')),
            array(array('hostname' => 'example.com', 'username' => 'test', 'password' => 'test')),
        );
    }
}
