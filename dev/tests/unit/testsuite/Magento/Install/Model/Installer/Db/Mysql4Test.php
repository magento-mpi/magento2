<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Install_Model_Installer_Db_Mysql4Test extends PHPUnit_Framework_TestCase
{
    /**
     * Test possible ways of declaring InnoDB engine by MySQL
     *
     * @dataProvider possibleEngines
     * @param array $supportedEngines
     * @param $expectedResult
     * @return void
     */
    public function testSupportEngine(array $supportedEngines, $expectedResult)
    {
        $connectionMock = $this->getMock('Magento\DB\Adapter\AdapterInterface');
        $resourceMock = $this->getMock('Magento\Core\Model\Resource', array('createConnection'), array(), '', false);
        $resourceMock->expects($this->once())->method('createConnection')->will($this->returnValue($connectionMock));

        $connectionMock->expects($this->once())->method('fetchPairs')->will($this->returnValue($supportedEngines));

        $installer = new \Magento\Install\Model\Installer\Db\Mysql4($resourceMock);
        $this->assertEquals($expectedResult, $installer->supportEngine());
    }

    /**
     * Data provider for returned engines from mysql and expectations.
     * @return array
     */
    public function possibleEngines()
    {
        return array(
            array(array('InnoDB' => 'DEFAULT'),  true),
            array(array('InnoDB' => 'YES'),      true),
            array(array('wrongEngine' => '123'), false)
        );
    }

    /**
     * @dataProvider getRequiredExtensionsDataProvider
     *
     * @param $config
     * @param $dbExtensions
     * @param $expectedResult
     */
    public function testGetRequiredExtensions($config, $dbExtensions, $expectedResult)
    {
        $resourceMock = $this->getMock('Magento_Core_Model_Resource', array(), array(), '', false);
        $installer = new Magento_Install_Model_Installer_Db_Mysql4($resourceMock, $dbExtensions);
        $installer->setConfig($config);
        $this->assertEquals($expectedResult, $installer->getRequiredExtensions());
    }

    /**
     * Data provider for testGetRequiredExtensions
     *
     * @return array
     */
    public function getRequiredExtensionsDataProvider()
    {
        return array(
            'wrong model' => array(
                array('db_model' => 'mysql66'),
                array('mysql' => array('pdo_test1')),
                array()
            ),
            'full extensions' => array(
                array('db_model' => 'mysql'),
                array('mysql' => array('pdo' => 'pdo_ext1', 'pdo_ext2', 'pdo2' => 'pdo_ext3')),
                array('pdo' => 'pdo_ext1', 'pdo_ext2', 'pdo2' => 'pdo_ext3')
            ),
            'empty extensions' => array(
                array('db_model' => 'mysql'),
                array('mysql' => array(), 'mysql2' => array('pdo_ext1', 'pdo_ext2')),
                array()
            )
        );
    }
}
