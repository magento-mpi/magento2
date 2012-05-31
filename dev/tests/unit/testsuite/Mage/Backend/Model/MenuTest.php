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

class Mage_Backend_Model_MenuTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu
     */
    protected  $_model;

    public function setUp()
    {
        $item = $this->getMock('Mage_Backend_Model_Menu_Item');
        $this->_model = new Mage_Backend_Model_Menu();
        $this->_model->addItem($item);
    }

    public function testNext()
    {

    }
}
