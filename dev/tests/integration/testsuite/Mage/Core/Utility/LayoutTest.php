<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Core
 */
class Mage_Core_Utility_LayoutTest extends PHPUnit_Framework_TestCase
{
    public function testGetLayoutUpdateFromFixture()
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);

        $layoutUpdate = $layoutUtility->getLayoutUpdateFromFixture(__DIR__ . '/_files/_layout_update.xml');
        $layoutElm = $layoutUpdate->getFileLayoutUpdatesXml(null, null, null);
        $layoutElm->prepare(null);

        $this->assertNotNull($layoutUpdate);
        $this->assertNotNull($layoutElm);
        foreach ($layoutElm->children() as $child) {
            if ($child->getName() == 'block' && $child->getBlockName() == 'root') {
                $this->assertEquals('Mage_Adminhtml_Block_Page', $child->getAttribute('class'));
                break;
            }
        }
    }

    public function testGetLayoutFromFixture()
    {
        $layoutUtility = new Mage_Core_Utility_Layout($this);

        $layout = $layoutUtility->getLayoutFromFixture(__DIR__ . '/_files/_layout_update.xml');
        $layout->generateXml()->generateBlocks();

        $this->assertNotNull($layout);
        $this->assertXmlStringEqualsXmlFile(__DIR__ . '/_files/_layout_update.xml', $layout->getUpdate()->getFileLayoutUpdatesXml(null, null, null)->asNiceXml());
        $this->assertNotNull($layout->getBlock('root'));
        $this->assertInstanceOf('Mage_Core_Block_Abstract', $layout->getBlock('root'));
        $this->assertEquals($layout->getBlock('messages')->getParentBlock(), $layout->getBlock('root'));
    }
}
