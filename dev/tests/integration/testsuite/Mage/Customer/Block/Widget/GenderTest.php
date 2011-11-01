<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Customer
 */
class Mage_Customer_Block_Widget_GenderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Block_Widget_Gender
     */
    protected $_block;

    public function setUp()
    {
        $this->_block = new Mage_Customer_Block_Widget_Gender;
    }

    public function tearDown()
    {
        $this->_block = null;
    }

    public function testGetGenderOptions()
    {
        $options = $this->_block->getGenderOptions();
        $this->assertInternalType('array', $options);
        $this->assertNotEmpty($options);
    }
}
