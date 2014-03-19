<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource;

class HelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Resource\Helper
     */
    protected $_model;

    /**
     * @var \Magento\DB\Select
     */
    protected $_select;

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Core\Model\Resource\Helper',
            array('modulePrefix' => 'core')
        );
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Store\Model\Resource\Collection');
        $this->_select = $collection->getSelect();
    }

    public function testPrepareColumnsList()
    {
        $columns = $this->_model->prepareColumnsList($this->_select);
        $this->assertContains('STORE_ID', array_keys($columns));
    }

    public function testAddGroupConcatColumn()
    {
        $select = (string)$this->_model->addGroupConcatColumn($this->_select, 'test_alias', 'store_id');
        $this->assertContains('GROUP_CONCAT', $select);
        $this->assertContains('test_alias', $select);
    }

    public function testGetDateDiff()
    {
        $diff = $this->_model->getDateDiff('2011-01-01', '2011-01-01');
        $this->assertInstanceOf('Zend_Db_Expr', $diff);
        $this->assertContains('TO_DAYS', (string) $diff);
    }

    public function testAddLikeEscape()
    {
        $value = $this->_model->addLikeEscape('test');
        $this->assertInstanceOf('Zend_Db_Expr', $value);
        $this->assertContains('test', (string) $value);
    }
}
