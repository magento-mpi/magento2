<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Model\Translate;

class InlineVdeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\DesignEditor\Model\Translate\InlineVde
     */
    protected $_model;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\DesignEditor\Helper\Data
     */
    protected $_helperData;

    public static function setUpBeforeClass()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\DesignInterface')
            ->setDesignTheme('magento_demo');
    }

    protected function setUp()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->_model = $objectManager->get('Magento\DesignEditor\Model\Translate\InlineVde');
        $this->_request = $objectManager->get('Magento\App\RequestInterface');
        $this->_request->setParam('translation_mode', 'text');

        $this->_helperData = $objectManager->get('Magento\DesignEditor\Helper\Data');
        $this->_helperData->setTranslationMode($this->_request);
    }

    public function testObjectCreation()
    {
        $this->assertInstanceOf('Magento\DesignEditor\Model\Translate\InlineVde', $this->_model);
        $this->assertInstanceOf('Magento\App\RequestInterface', $this->_request);
        $this->assertInstanceOf('Magento\DesignEditor\Helper\Data', $this->_helperData);
    }

    public function testIsAllowed()
    {
        // is allowed
        $this->assertTrue($this->_model->isAllowed());

        // is not allowed
        $this->_request->setParam('translation_mode', null);
        $this->_helperData->setTranslationMode($this->_request);
        $this->assertNull($this->_helperData->getTranslationMode());
        $this->assertFalse($this->_model->isAllowed());
    }

    /**
     * @dataProvider textTranslationMode
     */
    public function testTextTranslationMode($mode)
    {
        $this->_request->setParam('translation_mode', $mode);
        $this->_helperData->setTranslationMode($this->_request);
        $this->assertEquals($mode, $this->_helperData->getTranslationMode());
    }

    /**
     * Define the valid translation modes.
     *
     * @return array
     */
    public function textTranslationMode()
    {
        return array(
            array('text'),
            array('script'),
            array('alt')
        );
    }

    /**
     * @param string $originalText
     * @param string $expectedText
     * @dataProvider processResponseBodyTextDataProvider
     */
    public function testProcessResponseBodyText($originalText, $expectedText)
    {
        $actualText = $originalText;
        $this->_model->processResponseBody($actualText, false);

        $this->assertEquals($expectedText, $actualText);
    }

    /**
     * Define the expected text.
     *
     * @return array
     */
    public function processResponseBodyTextDataProvider()
    {
        return array(
            'plain text'  => array('text with no translations and tags', 'text with no translations and tags')
        );
    }

    /**
     * @param string $originalText
     * @dataProvider processResponseBodyHtmlDataProvider
     */
    public function testProcessResponseBodyHtml($originalText)
    {
        $actualText = $originalText;
        $this->_model->processResponseBody($actualText, false);

        // remove script preventing DomDocument load
        $actualText = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $actualText);

        $actual = new \DOMDocument;
        $actual->preserveWhiteSpace = FALSE;
        $actual->loadHTML($actualText);

        $xpath = new \DOMXPath($actual);
        // select all elements with data-translate attribute
        $translations = $xpath->query('//*[@data-translate]');

        // Ensure each data-translate element has a translate-mode attribute
        foreach ($translations as $translation) {
            $translateMode = $translation->getAttribute('data-translate-mode');
            $this->assertNotEmpty($translateMode);
            $this->assertTrue("text" == $translateMode || "script" == $translateMode || "alt" == $translateMode);
        }
    }

    /**
     * Define html text for test.
     *
     * @return array
     */
    public function processResponseBodyHtmlDataProvider()
    {
        $originalText = file_get_contents(__DIR__ . '/_files/_inline_page_original.html');

        return array(
            'html string' => array($originalText)
        );
    }
}
