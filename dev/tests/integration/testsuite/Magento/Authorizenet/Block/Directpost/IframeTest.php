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

namespace Magento\Authorizenet\Block\Directpost;

class IframeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtml()
    {
        $xssString = '</script><script>alert("XSS")</script>';
        /** @var $block \Magento\Authorizenet\Block\Directpost\Iframe */
        $block = \Mage::app()->getLayout()->createBlock('Magento\Authorizenet\Block\Directpost\Iframe');
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
