<?php
/**
 * {license_notice}
 *
 * @category    Varien
 * @package     Varien_Db
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Varien_Db_SelectTest extends Magento_Test_TestCase_ZendDbAdapterAbstract
{
    public function testWhere()
    {
        $select = new Varien_Db_Select($this->_getAdapterMockWithMockedQuote(1, "'5'"));
        $select->from('test')->where('field = ?', 5);
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (field = '5')", $select->assemble());

        $select = new Varien_Db_Select($this->_getAdapterMockWithMockedQuote(1, "''"));
        $select->from('test')->where('field = ?');
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (field = '')", $select->assemble());

        $select = new Varien_Db_Select($this->_getAdapterMockWithMockedQuote(1, "'%?%'"));
        $select->from('test')->where('field LIKE ?', '%value?%');
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (field LIKE '%?%')", $select->assemble());

        $select = new Varien_Db_Select($this->_getAdapterMockWithMockedQuote(0));
        $select->from('test')->where("field LIKE '%value?%'", null, Varien_Db_Select::TYPE_CONDITION);
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (field LIKE '%value?%')", $select->assemble());

        $select = new Varien_Db_Select($this->_getAdapterMockWithMockedQuote(1, "'1', '2', '4', '8'"));
        $select->from('test')->where("id IN (?)", array(1, 2, 4, 8));
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (id IN ('1', '2', '4', '8'))", $select->assemble());
    }

    /**
     * Retrieve mock of adapter with mocked quote method
     *
     * @param int $callCount
     * @param string|null $returnValue
     * @return Zend_Db_Adapter_Abstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAdapterMockWithMockedQuote($callCount, $returnValue = null)
    {
        $adapter = $this->_getAdapterMock('Zend_Db_Adapter_Pdo_Mysql', array('supportStraightJoin', 'quote'), null);
        $method = $adapter->expects($this->exactly($callCount))->method('quote');
        if ($callCount > 0) {
            $method->will($this->returnValue($returnValue));
        }
        return $adapter;
    }
}
