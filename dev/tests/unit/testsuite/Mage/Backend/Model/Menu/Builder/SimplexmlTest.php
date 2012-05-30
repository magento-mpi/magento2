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


class Mage_Backend_Model_Menu_Builder_SimplexmlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Model_Menu_BuilderAbstract
     */
    protected  $_model;

    public function setUp()
    {
        $factory = $this->getMock("Mage_Core_Model_Config", array(), array(), '', false);
        $factory->expects($this->any())
            ->method('getModelInstance')
            ->will(
                $this->returnCallback(function() {
                    return new Mage_Backend_Model_Menu_Item('<item/>');
                })
            );
        $this->_model = new Mage_Backend_Model_Menu_Builder_Simplexml(array(
            'factory' => $factory,
            'root' => new Varien_Simplexml_Element('<menu />')
        ));
    }

    public function testGetResult()
    {
        $this->_model->processCommand(new Mage_Backend_Model_Menu_Builder_Command_Add(array('id' => 1)));
        $this->_model->processCommand(
            new Mage_Backend_Model_Menu_Builder_Command_Add(array('id' => 2, 'parent' => 1))
        );
        $this->_model->processCommand(
            new Mage_Backend_Model_Menu_Builder_Command_Add(array('id' => 3, 'parent' => 2))
        );

        print_r($this->_model->getResult()->getNode()->asXML());
    }
}
