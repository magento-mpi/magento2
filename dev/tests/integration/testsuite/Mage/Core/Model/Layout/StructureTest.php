<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Layout_StructureTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Layout_Structure
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Core_Model_Layout_Structure;
    }

    /**
     * If several parents with the same name exist
     */
    public function testGetParentName()
    {
        $parent = 'parent';
        $child1 = 'child1';
        $child2 = 'child2';
        $this->_model->insertElement('', $parent, 'container');
        $this->assertEmpty($this->_model->getParentName($parent));

        $this->_model->insertElement($parent, $child1, 'block');
        $parentName = $this->_model->getParentName($child1);
        $this->assertEquals($parent, $parentName);

        $this->_model->insertElement('', $parent, 'block');
        $this->assertEmpty($this->_model->getParentName($parent));
        $this->_model->insertElement($parent, $child2, 'block');
        $parentName = $this->_model->getParentName($child2);
        $this->assertEquals($parent, $parentName);
    }
}
