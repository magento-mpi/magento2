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

class Mage_Backend_Model_Menu_Builder_Command_CreateTest extends PHPUnit_Framework_TestCase
{
    protected $_model;

    protected $_params = array(
        'id' => 'item'
    );

    public function setUp()
    {
        $this->_model = new Mage_Backend_Model_Menu_Builder_Command_Create($this->_params);
    }

    public function testExecuteCreatesEmptyRootItem()
    {
        $params = $this->_model->execute(array());
        $this->assertEquals($this->_params, $params);
    }
}
