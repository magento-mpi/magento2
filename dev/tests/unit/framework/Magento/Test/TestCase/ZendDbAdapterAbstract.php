<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Test_TestCase_ZendDbAdapterAbstract extends PHPUnit_Framework_TestCase
{
    /**
     * Create an adapter mock object
     *
     * @param string $adapterClass
     * @param array $mockMethods
     * @param array|null $constructArgs
     * @param string $mockStatementMethods
     * @return Zend_Db_Adapter_Abstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAdapterMock($adapterClass, $mockMethods, $constructArgs = array(),
        $mockStatementMethods = 'execute'
    ) {
        if (empty($constructArgs)) {
            $adapter = $this->getMock($adapterClass, $mockMethods, array(), '', false);
        } else {
            $adapter = $this->getMock($adapterClass, $mockMethods, $constructArgs);
        }
        if (null !== $mockStatementMethods) {
            $statement = $this->getMock('Zend_Db_Statement', array_merge((array)$mockStatementMethods,
                    array('closeCursor', 'columnCount', 'errorCode', 'errorInfo', 'fetch', 'nextRowset', 'rowCount')
                ), array(), '', false
            );
            $adapter->expects($this->any())
                ->method('query')
                ->will($this->returnValue($statement));
        }
        return $adapter;
    }
}
