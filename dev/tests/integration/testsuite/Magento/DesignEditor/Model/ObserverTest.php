<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for \Magento\DesignEditor\Model\Observer
 */
namespace Magento\DesignEditor\Model;

class ObserverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $area
     * @param string $designMode
     * @param array $expectedAssets
     *
     * @magentoAppIsolation enabled
     * @dataProvider cleanJsDataProvider
     */
    public function testCleanJs($area, $designMode, $expectedAssets)
    {
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        /** @var $headBlock \Magento\Theme\Block\Html\Head */
        $headBlock = $layout->createBlock('Magento\Theme\Block\Html\Head', 'head');
        $headBlock->setData('vde_design_mode', $designMode);

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\View\Asset\Service $assetService */
        $assetService = $objectManager->create('Magento\View\Asset\Service');

        /** @var $pageAssets \Magento\View\Asset\GroupedCollection */
        $pageAssets = $objectManager->get('Magento\View\Asset\GroupedCollection');

        $fixtureAssets = array(
            array('file' => 'test.css', 'params' => array()),
            array('file' => 'test_vde.css', 'params' => array('flag_name' => 'vde_design_mode')),
            array('file' => 'test.js', 'params' => array()),
            array('file' => 'test_vde.js', 'params' => array('flag_name' => 'vde_design_mode')),
        );

        foreach ($fixtureAssets as $asset) {
            $pageAssets->add(
                $asset['file'], $assetService->createAsset($asset['file']), $asset['params']
            );
        }

        /** @var \Magento\Config\Scope $configScope */
        $configScope = $objectManager->get('Magento\Config\ScopeInterface');
        $configScope->setCurrentScope($area);

        /** @var $eventManager \Magento\Event\ManagerInterface */
        $eventManager = $objectManager->get('Magento\Event\ManagerInterface');
        $eventManager->dispatch('controller_action_layout_generate_blocks_after', array('layout' => $layout));

        $actualAssets = array_keys($pageAssets->getAll());
        $this->assertEquals($expectedAssets, $actualAssets);
    }

    /**
     * @return array
     */
    public function cleanJsDataProvider()
    {
        return array(
            'vde area - design mode' => array('vde', '1', array('test.css', 'test_vde.css', 'test_vde.js')),
            'vde area - non design mode' => array('vde', '0',
                array('test.css', 'test_vde.css', 'test.js', 'test_vde.js')),
            'default area - design mode' => array('default', '1',
                array('test.css', 'test_vde.css', 'test.js', 'test_vde.js')),
            'default area - non design mode' => array('default', '0',
                array('test.css', 'test_vde.css', 'test.js', 'test_vde.js')),
        );
    }
}
