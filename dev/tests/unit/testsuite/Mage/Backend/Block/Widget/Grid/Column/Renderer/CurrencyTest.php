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

class Mage_Backend_Block_Widget_Grid_Column_Renderer_CurrencyTest extends PHPUnit_Framework_TestCase
{
    protected $_blockCurrency;

    protected function setUp()
    {
        $this->_blockCurrency = new Mage_Backend_Block_Widget_Grid_Column_Renderer_Currency();
    }

    public function testRender()
    {
        $this->assertTrue(true);
    }
}
