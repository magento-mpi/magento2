<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Translate_InlineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Translate_Inline
     */
    protected $_model;

    protected $_storeId = 'default';

    public static function setUpBeforeClass()
    {
        Mage::getDesign()->setDesignTheme('default/default/default');
    }

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Translate_Inline();
        /* Called getConfig as workaround for setConfig bug */
        Mage::app()->getStore($this->_storeId)->getConfig('dev/translate_inline/active');
        Mage::app()->getStore($this->_storeId)->setConfig('dev/translate_inline/active', true);
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testIsAllowed()
    {
        $this->assertTrue($this->_model->isAllowed());
        $this->assertTrue($this->_model->isAllowed($this->_storeId));
        $this->assertTrue($this->_model->isAllowed(Mage::app()->getStore($this->_storeId)));
    }

    /**
     * @dataProvider processAjaxPostDataProvider
     */
    public function testProcessAjaxPost($originalText, $translatedText, $isPerStore = null)
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $inputArray = array(array('original' => $originalText, 'custom' => $translatedText));
        if ($isPerStore !== null) {
            $inputArray[0]['perstore'] = $isPerStore;
        }
        $this->_model->processAjaxPost($inputArray);

        $model = new Mage_Core_Model_Translate_String();
        $model->load($originalText);
        try {
            $this->assertEquals($translatedText, $model->getTranslate());
            $model->delete();
        } catch (Exception $e) {
            Mage::logException($e);
            $model->delete();
        }
    }

    public function processAjaxPostDataProvider()
    {
        return array(
            array('original text 1', 'translated text 1'),
            array('original text 2', 'translated text 2', true),
        );
    }

    /**
     * @dataProvider stripInlineTranslationsDataProvider
     */
    public function testStripInlineTranslations($originalText, $expectedText)
    {
        $actualText = $originalText;
        $this->_model->stripInlineTranslations($actualText);
        $this->assertEquals($expectedText, $actualText);
    }

    public function stripInlineTranslationsDataProvider()
    {
        $originalText = '{{{first}}{{second}}{{third}}{{fourth}}}';
        return array(
            array($originalText, 'first'),
            array(array($originalText), array('first')),
        );
    }

    /**
     * @param string $originalText
     * @param string $expectedText
     * @dataProvider processResponseBodyDataProvider
     */
    public function testProcessResponseBody($originalText, $expectedText)
    {
        $actualText = $originalText;
        $this->_model->processResponseBody($actualText);
        $this->markTestIncomplete('Bug MAGE-2494');

        $expected = new DOMDocument;
        $expected->preserveWhiteSpace = FALSE;
        $expected->loadHTML($expectedText);

        $actual = new DOMDocument;
        $actual->preserveWhiteSpace = FALSE;
        $actual->loadHTML($actualText);

        $this->assertEquals($expected, $actual);
    }

    public function processResponseBodyDataProvider()
    {
        $originalText = file_get_contents(__DIR__ . '/_files/_inline_page_original.html');
        $expectedText = file_get_contents(__DIR__ . '/_files/_inline_page_expected.html');
        $expectedText = str_replace('{{design_package}}', Mage::getDesign()->getPackageName(), $expectedText);
        return array(
            'plain text'  => array('text with no translations and tags', 'text with no translations and tags'),
            'html string' => array($originalText, $expectedText),
            'html array'  => array(array($originalText), array($expectedText)),
        );
    }

    public function testSetGetIsJson()
    {
        $this->assertFalse($this->_model->getIsJson());
        $this->_model->setIsJson(true);
        $this->assertTrue($this->_model->getIsJson());
    }
}
