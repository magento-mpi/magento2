<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\DB;

use Magento\TestFramework\Helper\ObjectManager;

class SelectTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMatchQuery()
    {
        /** @var Select $select */
        $select = (new ObjectManager($this))->getObject('Magento\Framework\DB\Select');

        $result = $select->getMatchQuery(
            ['title', 'description'],
            'some searchable text',
            Select::FULLTEXT_MODE_NATURAL
        );
        $expectedResult = "MATCH ('title,description') AGAINST ('some searchable text' IN NATURAL LANGUAGE MODE)";

        $this->assertEquals($expectedResult, $result);
    }

    public function testWhere()
    {
        $select = new Select($this->_getAdapterMockWithMockedQuote(1, "'5'"));
        $select->from('test')->where('field = ?', 5);
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (field = '5')", $select->assemble());

        $select = new Select($this->_getAdapterMockWithMockedQuote(1, "''"));
        $select->from('test')->where('field = ?');
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (field = '')", $select->assemble());

        $select = new Select($this->_getAdapterMockWithMockedQuote(1, "'%?%'"));
        $select->from('test')->where('field LIKE ?', '%value?%');
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (field LIKE '%?%')", $select->assemble());

        $select = new Select($this->_getAdapterMockWithMockedQuote(0));
        $select->from('test')->where("field LIKE '%value?%'", null, Select::TYPE_CONDITION);
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (field LIKE '%value?%')", $select->assemble());

        $select = new Select($this->_getAdapterMockWithMockedQuote(1, "'1', '2', '4', '8'"));
        $select->from('test')->where("id IN (?)", array(1, 2, 4, 8));
        $this->assertEquals("SELECT `test`.* FROM `test` WHERE (id IN ('1', '2', '4', '8'))", $select->assemble());
    }

    /**
     * Retrieve mock of adapter with mocked quote method
     *
     * @param int $callCount
     * @param string|null $returnValue
     * @return \Zend_Db_Adapter_Abstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAdapterMockWithMockedQuote($callCount, $returnValue = null)
    {
        $adapter = $this->getMock(
            'Zend_Db_Adapter_Pdo_Mysql',
            array('supportStraightJoin', 'quote'),
            array(),
            '',
            false
        );
        $method = $adapter->expects($this->exactly($callCount))->method('quote');
        if ($callCount > 0) {
            $method->will($this->returnValue($returnValue));
        }
        return $adapter;
    }
}
