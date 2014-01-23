<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Utility;

class LayoutTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Utility\Layout
     */
    protected $_utility;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
            \Magento\App\Filesystem::PARAM_APP_DIRS => array(
                \Magento\App\Filesystem::APP_DIR => array('path' => BP . '/dev/tests/integration'),
            )
        ));
        $this->_utility = new \Magento\Core\Utility\Layout($this);
    }

    /**
     * Assert that the actual layout update instance represents the expected layout update file
     *
     * @param \Magento\Core\Model\Layout\Merge $actualUpdate
     * @param string $expectedUpdateFile
     * @param \Magento\Core\Model\Layout\Merge $actualUpdate
     */
    protected function _assertLayoutUpdate($expectedUpdateFile, $actualUpdate)
    {
        $this->assertInstanceOf('Magento\View\Layout\ProcessorInterface', $actualUpdate);

        $layoutUpdateXml = $actualUpdate->getFileLayoutUpdatesXml();
        $this->assertInstanceOf('Magento\View\Layout\Element', $layoutUpdateXml);
        $this->assertXmlStringEqualsXmlFile($expectedUpdateFile, $layoutUpdateXml->asNiceXml());
    }

    /**
     * @dataProvider getLayoutFromFixtureDataProvider
     * @param string|array $inputFiles
     * @param string $expectedFile
     */
    public function testGetLayoutUpdateFromFixture($inputFiles, $expectedFile)
    {
        $layoutUpdate = $this->_utility->getLayoutUpdateFromFixture($inputFiles);
        $this->_assertLayoutUpdate($expectedFile, $layoutUpdate);
    }

    /**
     * @dataProvider getLayoutFromFixtureDataProvider
     * @param string|array $inputFiles
     * @param string $expectedFile
     */
    public function testGetLayoutFromFixture($inputFiles, $expectedFile)
    {
        $layout = $this->_utility->getLayoutFromFixture($inputFiles, $this->_utility->getLayoutDependencies());
        $this->assertInstanceOf('Magento\View\LayoutInterface', $layout);
        $this->_assertLayoutUpdate($expectedFile, $layout->getUpdate());
    }

    public function getLayoutFromFixtureDataProvider()
    {
        return array(
            'single fixture file' => array(
                __DIR__ . '/_files/layout/handle_two.xml', __DIR__ . '/_files/layout_merged/single_handle.xml'
            ),
            'multiple fixture files' => array(
                glob(__DIR__ . '/_files/layout/*.xml'), __DIR__ . '/_files/layout_merged/multiple_handles.xml'
            ),
        );
    }
}
