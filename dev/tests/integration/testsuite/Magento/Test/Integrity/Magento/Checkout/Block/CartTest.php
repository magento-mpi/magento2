<?php
/**
 * Integrity test for template setters in \Magento\Checkout\Block\CartTest
 *
 * {license_notice}
 *
 * @category Magento
 * @package Magento_Checkout
 * @subpackage integration_tests
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Test\Integrity\Magento\Checkout\Block;

class CartTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $layoutFile
     * @dataProvider layoutFilesDataProvider
     */
    public function testCustomTemplateSetters($layoutFile)
    {
        $params = array();
        if (preg_match('/app\/design\/frontend\/(.+?)\/(.+?)\//', $layoutFile, $matches)) {
            $params = array('theme' => $matches[1]);
        }

        $xml = simplexml_load_file($layoutFile);
        $nodes = $xml->xpath('//block/action[@method="setCartTemplate" or @method="setEmptyTemplate"]') ?: array();
        /** @var $node \SimpleXMLElement */
        foreach ($nodes as $node) {
            $template = (array)$node->children();
            $template = array_shift($template);
            $blockNode = $node->xpath('..');
            $blockNode = $blockNode[0];
            preg_match('/^(.+?\\\\.+?)\\\\/', $blockNode['class'], $matches);
            $params['module'] = str_replace(\Magento\Autoload\IncludePath::NS_SEPARATOR, '_', $matches[1]);
            $this->assertFileExists(
                \Magento\TestFramework\Helper\Bootstrap::getObjectmanager()->get('Magento\Core\Model\View\FileSystem')
                    ->getFilename($template, $params)
            );
        }
    }

    /**
     * @return array
     */
    public function layoutFilesDataProvider()
    {
        return \Magento\TestFramework\Utility\Files::init()->getLayoutFiles(array('area' => 'frontend'));
    }
}
