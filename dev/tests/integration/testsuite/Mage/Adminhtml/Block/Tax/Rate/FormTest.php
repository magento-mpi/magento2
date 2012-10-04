<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Tax_Rate_FormTest extends PHPUnit_Framework_TestCase
{
    public function testGetRateCollection()
    {
        $layout = new Mage_Core_Model_Layout();
        /** @var $block Mage_Adminhtml_Block_Tax_Rate_Form */
        $block = $layout->createBlock('Mage_Adminhtml_Block_Tax_Rate_Form', 'block');
        $this->assertInstanceOf('Mage_Tax_Model_Resource_Calculation_Rate_Collection', $block->getRateCollection());
    }
}
