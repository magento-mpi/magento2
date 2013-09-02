<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Translate_InlineParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Translate_InlineParser
     */
    protected $_inlineParser;

    /** @var string */
    protected $_storeId = 'default';

    public static function setUpBeforeClass()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setDesignTheme('magento_demo');
    }

    public function setUp()
    {
        $this->_inlineParser = Mage::getModel('Magento_Core_Model_Translate_InlineParser');
        /* Called getConfig as workaround for setConfig bug */
        Mage::app()->getStore($this->_storeId)->getConfig('dev/translate_inline/active');
        Mage::app()->getStore($this->_storeId)->setConfig('dev/translate_inline/active', true);
    }

    /**
     * @dataProvider processAjaxPostDataProvider
     */
    public function testProcessAjaxPost($originalText, $translatedText, $isPerStore = null)
    {
        $inputArray = array(array('original' => $originalText, 'custom' => $translatedText));
        if ($isPerStore !== null) {
            $inputArray[0]['perstore'] = $isPerStore;
        }
        /** @var $inline Magento_Core_Model_Translate_Inline */
        $inline = Mage::getModel('Magento_Core_Model_Translate_Inline');
        $this->_inlineParser->processAjaxPost($inputArray, $inline);

        $model = Mage::getModel('Magento_Core_Model_Translate_String');
        $model->load($originalText);
        try {
            $this->assertEquals($translatedText, $model->getTranslate());
            $model->delete();
        } catch (Exception $e) {
            $model->delete();
            throw $e;
        }
    }

    /**
     * @return array
     */
    public function processAjaxPostDataProvider()
    {
        return array(
            array('original text 1', 'translated text 1'),
            array('original text 2', 'translated text 2', true),
        );
    }

    public function testSetGetIsJson()
    {
        $isJsonProperty = new ReflectionProperty(get_class($this->_inlineParser), '_isJson');
        $isJsonProperty->setAccessible(true);

        $this->assertFalse($isJsonProperty->getValue($this->_inlineParser));

        $setIsJsonMethod = new ReflectionMethod($this->_inlineParser, 'setIsJson');
        $setIsJsonMethod->setAccessible(true);
        $setIsJsonMethod->invoke($this->_inlineParser, true);

        $this->assertTrue($isJsonProperty->getValue($this->_inlineParser));
    }
}
