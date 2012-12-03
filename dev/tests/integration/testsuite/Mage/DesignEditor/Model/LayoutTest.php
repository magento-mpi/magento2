<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for VDE layout
 */
class Mage_DesignEditor_Model_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_DesignEditor_Model_Layout::sanitizeLayout
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage Invalid block type: Namespace_Module_Block_NotSafe
     */
    public function testGenerateElements()
    {
        $layout = $this->_getLayoutWithTestUpdate();
        $layout->generateElements();

        $this->assertStringMatchesFormatFile(__DIR__ . '/_files/expected_layout_update.xml',
            $layout->getNode()->asNiceXml()
        );

        $layout = $this->_getLayoutWithTestUpdate(false);
        $layout->generateElements();
    }

    /**
     * Retrieve test layout with test layout update
     *
     * @param bool $isSanitizeBlocks
     * @return Mage_DesignEditor_Model_Layout
     */
    protected function _getLayoutWithTestUpdate($isSanitizeBlocks = true)
    {
        $arguments = array(
            'isSanitizeBlocks' => $isSanitizeBlocks
        );
        /** @var $layout Mage_DesignEditor_Model_Layout */
        $layout = Mage::getObjectManager()->create('Mage_DesignEditor_Model_Layout', $arguments);
        $layout->getUpdate()->addUpdate(file_get_contents(__DIR__ . '/_files/layout_update.xml'));
        $layout->generateXml();

        return $layout;
    }
}