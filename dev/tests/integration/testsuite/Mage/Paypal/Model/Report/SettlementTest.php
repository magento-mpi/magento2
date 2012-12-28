<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Paypal_Model_Report_SettlementTest extends PHPUnit_Framework_TestCase
{
    public function testFetchAndSave()
    {
        /** @var $model Mage_Paypal_Model_Report_Settlement; */
        $model = Mage::getModel('Mage_Paypal_Model_Report_Settlement');
        $connection = $this->getMock('Varien_Io_Sftp', array('rawls', 'read'), array(), '', false);
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
        Mage_Paypal_Model_Report_Settlement::createConnection($config);
    }

    /**
     * @return array
     */
    public function createConnectionExceptionDataProvider()
    {
        return array(
            array(array()),
            array(array('username', 'password', 'path')),
            array(array('hostname', 'password', 'path')),
            array(array('hostname', 'username', 'path')),
            array(array('hostname', 'username', 'password')),
        );
    }
}
