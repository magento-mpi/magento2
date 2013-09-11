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
class Magento_DesignEditor_Model_ObserverTest extends PHPUnit_Framework_TestCase
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
        $layout = Mage::app()->getLayout();
        /** @var $headBlock \Magento\Page\Block\Html\Head */
        $headBlock = $layout->createBlock('\Magento\Page\Block\Html\Head', 'head');
        $headBlock->setData('vde_design_mode', $designMode);

        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        /** @var $page \Magento\Core\Model\Page */
        $page = $objectManager->get('Magento\Core\Model\Page');

        /** @var $pageAssets \Magento\Page\Model\Asset\GroupedCollection */
        $pageAssets = $page->getAssets();

        $fixtureAssets = array(
            array('name'   => 'test_css', 'type' => \Magento\Core\Model\View\Publisher::CONTENT_TYPE_CSS,
                  'params' => array()),
            array('name'   => 'test_css_vde', 'type' => \Magento\Core\Model\View\Publisher::CONTENT_TYPE_CSS,
                  'params' => array('flag_name' => 'vde_design_mode')),
            array('name'   => 'test_js', 'type' => \Magento\Core\Model\View\Publisher::CONTENT_TYPE_JS,
                  'params' => array()),
            array('name'   => 'test_js_vde', 'type' => \Magento\Core\Model\View\Publisher::CONTENT_TYPE_JS,
                  'params' => array('flag_name' => 'vde_design_mode')),
        );

        foreach ($fixtureAssets as $asset) {
            $pageAssets->add(
                $asset['name'],
                $objectManager->create('Magento\Core\Model\Page\Asset\ViewFile', array(
                    'file' => 'some_file',
                    'contentType' => $asset['type'],
                )),
                $asset['params']
            );
        }


        /** @var \Magento\Core\Model\Config\Scope $configScope */
        $configScope = $objectManager->get('Magento\Core\Model\Config\Scope');
        $configScope->setCurrentScope($area);

        /** @var $eventManager \Magento\Core\Model\Event\Manager */
        $eventManager = $objectManager->get('Magento\Core\Model\Event\Manager');
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
            'vde area - design mode' => array('vde', '1', array('test_css', 'test_css_vde', 'test_js_vde')),
            'vde area - non design mode' => array('vde', '0',
                array('test_css', 'test_css_vde', 'test_js', 'test_js_vde')),
            'default area - design mode' => array('default', '1',
                array('test_css', 'test_css_vde', 'test_js', 'test_js_vde')),
            'default area - non design mode' => array('default', '0',
                array('test_css', 'test_css_vde', 'test_js', 'test_js_vde')),
        );
    }
}
