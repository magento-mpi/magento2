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

class Magento_Core_Utility_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Utility_Layout
     */
    protected $_utility;

    protected function setUp()
    {
        $this->_utility = new Magento_Core_Utility_Layout($this);
    }

    /**
     * Assert that the actual layout update instance represents the expected layout update file
     *
     * @param \Magento\Core\Model\Layout\Merge $actualUpdate
     * @param string $expectedUpdateFile
     */
    protected function _assertLayoutUpdate($actualUpdate, $expectedUpdateFile)
    {
        $this->assertInstanceOf('Magento\Core\Model\Layout\Merge', $actualUpdate);

        $layoutUpdateXml = $actualUpdate->getFileLayoutUpdatesXml();
        $this->assertInstanceOf('Magento\Core\Model\Layout\Element', $layoutUpdateXml);
        $this->assertXmlStringEqualsXmlFile($expectedUpdateFile, $layoutUpdateXml->asNiceXml());
    }

    public function testGetLayoutUpdateFromFixture()
    {
        $layoutUpdateFile = __DIR__ . '/_files/_layout_update.xml';
        $layoutUpdate = $this->_utility->getLayoutUpdateFromFixture($layoutUpdateFile);
        $this->_assertLayoutUpdate($layoutUpdate, $layoutUpdateFile);
    }

    public function testGetLayoutFromFixture()
    {
        $layoutUpdateFile = __DIR__ . '/_files/_layout_update.xml';
        $layout = $this->_utility->getLayoutFromFixture($layoutUpdateFile, $this->_utility->getLayoutDependencies());
        $this->assertInstanceOf('Magento\Core\Model\Layout', $layout);
        $this->_assertLayoutUpdate($layout->getUpdate(), $layoutUpdateFile);
    }
}
