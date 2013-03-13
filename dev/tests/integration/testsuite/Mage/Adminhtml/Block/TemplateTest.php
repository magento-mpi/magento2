<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_TemplateTest extends Mage_Backend_Area_TestCase
{
    public function testConstructor()
    {
        $this->assertInstanceOf(
            'Mage_Backend_Block_Template',
            Mage::app()->getLayout()->createBlock('Mage_Adminhtml_Block_Template')
        );
    }
}
