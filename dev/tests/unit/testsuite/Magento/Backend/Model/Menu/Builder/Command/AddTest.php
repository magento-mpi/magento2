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

class Magento_Backend_Model_Menu_Builder_Command_AddTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Model_Menu_Builder_Command_Add
     */
    protected $_model;

    protected $_params = array(
        'id' => 'item',
        'title' => 'item',
        'module' => 'Magento_Backend',
        'parent' => 'parent',
        'resource' => 'Magento_Backend::item'
    );

    public function setUp()
    {
        $this->_model = new Magento_Backend_Model_Menu_Builder_Command_Add($this->_params);
    }

    public function testExecuteFillsEmptyItemWithData()
    {
        $params = $this->_model->execute(array());
        $this->assertEquals($this->_params, $params);
    }

    public function testExecuteDoesntRewriteDataInFilledItem()
    {
        $params = $this->_model->execute(array('title' => 'newitem'));
        $this->_params['title'] =  'newitem';
        $this->assertEquals($this->_params, $params);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testChainWithAnotherAddCommandTrowsException()
    {
        $this->_model->chain(new Magento_Backend_Model_Menu_Builder_Command_Add($this->_params));
    }
}
