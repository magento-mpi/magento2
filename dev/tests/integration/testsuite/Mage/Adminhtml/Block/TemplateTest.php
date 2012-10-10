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

/**
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $this->assertInstanceOf('Mage_Backend_Block_Template', Mage::getModel('Mage_Adminhtml_Block_Template'));
    }
}
