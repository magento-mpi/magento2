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

namespace Magento\Core\Model\Translate;

class InlineParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Translate\Model\Inline\Parser
     */
    protected $_inlineParser;

    /** @var string */
    protected $_storeId = 'default';

    public static function setUpBeforeClass()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\DesignInterface')
            ->setDesignTheme('magento_blank');
    }

    protected function setUp()
    {
        /** @var $inline \Magento\Translate\Inline */
        $inline = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Translate\Inline');
        $this->_inlineParser = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Translate\Model\Inline\Parser', array('translateInline' => $inline));
        /* Called getConfig as workaround for setConfig bug */
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore($this->_storeId)->getConfig('dev/translate_inline/active');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\StoreManagerInterface')
            ->getStore($this->_storeId)->setConfig('dev/translate_inline/active', true);
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
        $this->_inlineParser->processAjaxPost($inputArray);

        $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Translate\Model\String');
        $model->load($originalText);
        try {
            $this->assertEquals($translatedText, $model->getTranslate());
            $model->delete();
        } catch (\Exception $e) {
            $model->delete();
            \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Logger')
                ->logException($e);
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
        $isJsonProperty = new \ReflectionProperty(get_class($this->_inlineParser), '_isJson');
        $isJsonProperty->setAccessible(true);

        $this->assertFalse($isJsonProperty->getValue($this->_inlineParser));

        $setIsJsonMethod = new \ReflectionMethod($this->_inlineParser, 'setIsJson');
        $setIsJsonMethod->setAccessible(true);
        $setIsJsonMethod->invoke($this->_inlineParser, true);

        $this->assertTrue($isJsonProperty->getValue($this->_inlineParser));
    }
}
