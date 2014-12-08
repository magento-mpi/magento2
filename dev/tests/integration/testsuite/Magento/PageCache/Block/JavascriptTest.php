<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Block;

/**
 * Class JavascriptTest
 */
class JavascriptTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\PageCache\Block\Javascript
     */
    protected $javascript;

    protected function setUp()
    {
        $this->javascript = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\PageCache\Block\Javascript'
        );
    }

    public function testGetScriptOptions()
    {
        $_GET['getparameter'] = 1;
        $this->assertContains('?getparameter=1', $this->javascript->getScriptOptions());
    }
}
