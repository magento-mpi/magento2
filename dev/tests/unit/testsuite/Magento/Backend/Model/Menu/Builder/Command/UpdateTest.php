<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Model_Menu_Builder_Command_UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Backend\Model\Menu\Builder\Command\Update
     */
    protected $_model;

    protected $_params = array(
        'id' => 'item', 'title' => 'item', 'module' => 'Magento_Backend', 'parent' => 'parent'
    );

    protected function setUp()
    {
        $this->_model = new \Magento\Backend\Model\Menu\Builder\Command\Update($this->_params);
    }

    public function testExecuteFillsEmptyItemWithData()
    {
        $params = $this->_model->execute(array());
        $this->assertEquals($this->_params, $params);
    }

    public function testExecuteRewritesDataInFilledItem()
    {
        $params = $this->_model->execute(array('title' => 'newitem'));
        $this->assertEquals($this->_params, $params);
    }
}
