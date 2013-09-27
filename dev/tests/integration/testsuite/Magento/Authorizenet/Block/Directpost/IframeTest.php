<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Authorizenet_Block_Directpost_IframeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        $xssString = '</script><script>alert("XSS")</script>';
        /** @var $block Magento_Authorizenet_Block_Directpost_Iframe */
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Authorizenet_Block_Directpost_Iframe');
        $block->setTemplate('directpost/iframe.phtml');
        $block->setParams(array(
            'redirect' => $xssString,
            'redirect_parent' => $xssString,
            'error_msg' => $xssString,
        ));
        $content = $block->toHtml();
        $this->assertNotContains($xssString, $content, 'Params mast be escaped');
        $this->assertContains(htmlspecialchars($xssString), $content, 'Content must present');
    }
}
