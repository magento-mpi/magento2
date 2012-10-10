<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_LinksTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetUploadButtonsHtml()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $block = Mage::app()->getLayout()->createBlock(
            'Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Links'
        );
        self::performUploadButtonTest($block);
    }

    /**
     * Reuse code for testing getUploadButtonHtml()
     *
     * @param Mage_Core_Block_Abstract $block
     */
    public static function performUploadButtonTest(Mage_Core_Block_Abstract $block)
    {
        self::markTestIncomplete('Need to fix DI dependencies + block');

        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $layout->addBlock($block, 'links');
        $expected = uniqid();
        $text = Mage::app()->getLayout()->createBlock('Mage_Core_Block_Text',
            array('data' => array('text' => $expected))
        );
        $block->unsetChild('upload_button');
        $layout->addBlock($text, 'upload_button', 'links');
        self::assertEquals($expected, $block->getUploadButtonHtml());
    }
}
