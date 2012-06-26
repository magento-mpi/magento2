<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Menu_Builder_Command_UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_Builder_Command_Update
     */
    protected $_model;

    protected $_params = array(
        'id' => 'item', 'title' => 'item', 'module' => 'Mage_Backend', 'parent' => 'parent'
    );

    public function setUp()
    {
        $this->_model = new Mage_Backend_Model_Menu_Builder_Command_Update($this->_params);
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
