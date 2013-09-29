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

namespace Magento\Install\Model\Installer\Db;

class Mysql4Test extends \PHPUnit_Framework_TestCase
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
        $resourceMock = $this->getMock('Magento\Core\Model\Resource\Type\Db\Pdo\Mysql', array(), array(), '', false);
        $adapterFactoryMock = $this->getMock('Magento\Core\Model\Resource\Type\Db\Pdo\MysqlFactory',
            array('create', 'getConnectionData', 'getConnection'), array(), '', false);
        $adapterFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($resourceMock));
        $adapterFactoryMock->expects($this->any())->method('getConnectionData')
            ->will($this->returnValue(array()));
        $resourceMock->expects($this->once())->method('getConnection')
            ->will($this->returnValue($connectionMock));

        $connectionMock->expects($this->once())->method('fetchPairs')->will($this->returnValue($supportedEngines));

        $installer = new \Magento\Install\Model\Installer\Db\Mysql4($adapterFactoryMock);
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
        $adapterFactoryMock = $this->getMock('Magento\Core\Model\Resource\Type\Db\Pdo\MysqlFactory',
            array(), array(), '', false);
        $installer = new \Magento\Install\Model\Installer\Db\Mysql4($adapterFactoryMock, $dbExtensions);
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
