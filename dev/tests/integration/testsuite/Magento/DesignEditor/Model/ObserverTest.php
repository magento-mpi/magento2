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
        /** @var \Magento\Framework\Registry $registry */
        $registry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\Registry'
        );
        $registry->register('vde_design_mode', $designMode);

        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        /** @var \Magento\Framework\View\Asset\Repository $assetRepo */
        $assetRepo = $objectManager->create('Magento\Framework\View\Asset\Repository');

        /** @var $pageAssets \Magento\Framework\View\Asset\GroupedCollection */
        $pageAssets = $objectManager->get('Magento\Framework\View\Asset\GroupedCollection');

        $fixtureAssets = array(
            array('file' => 'test.css', 'params' => array()),
            array('file' => 'test_vde.css', 'params' => array('flag_name' => 'vde_design_mode')),
            array('file' => 'test.js', 'params' => array()),
            array('file' => 'test_vde.js', 'params' => array('flag_name' => 'vde_design_mode')),
        );

        foreach ($fixtureAssets as $asset) {
            $pageAssets->add(
                $asset['file'],
                $assetRepo->createAsset($asset['file']),
                $asset['params']
            );
        }

        /** @var \Magento\Framework\Config\Scope $configScope */
        $configScope = $objectManager->get('Magento\Framework\Config\ScopeInterface');
        $configScope->setCurrentScope($area);

        /** @var $eventManager \Magento\Framework\Event\ManagerInterface */
        $eventManager = $objectManager->get('Magento\Framework\Event\ManagerInterface');
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
