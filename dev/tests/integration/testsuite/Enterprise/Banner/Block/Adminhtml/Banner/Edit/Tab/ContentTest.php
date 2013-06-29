<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_ContentTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $this->assertInstanceOf(
            'Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Content',
            Mage::app()->getLayout()->createBlock('Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Content')
        );
    }
}
