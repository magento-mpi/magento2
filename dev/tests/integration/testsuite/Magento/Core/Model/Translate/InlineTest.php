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

class InlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Translate\Inline
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_storeId = 'default';

    public static function setUpBeforeClass()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setDesignTheme('magento_demo');
    }

    public function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Core\Model\Translate\Inline');
        /* Called getConfig as workaround for setConfig bug */
        \Mage::app()->getStore($this->_storeId)->getConfig('dev/translate_inline/active');
        \Mage::app()->getStore($this->_storeId)->setConfig('dev/translate_inline/active', true);
    }

    public function testIsAllowed()
    {
        $this->assertTrue($this->_model->isAllowed());
        $this->assertTrue($this->_model->isAllowed($this->_storeId));
        $this->assertTrue($this->_model->isAllowed(\Mage::app()->getStore($this->_storeId)));
    }

    /**
     * @param string $originalText
     * @param string $expectedText
     * @dataProvider processResponseBodyDataProvider
     */
    public function testProcessResponseBody($originalText, $expectedText)
    {
        $actualText = $originalText;
        $this->_model->processResponseBody($actualText, false);
        $this->markTestIncomplete('Bug MAGE-2494');

        $expected = new \DOMDocument;
        $expected->preserveWhiteSpace = FALSE;
        $expected->loadHTML($expectedText);

        $actual = new \DOMDocument;
        $actual->preserveWhiteSpace = FALSE;
        $actual->loadHTML($actualText);

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function processResponseBodyDataProvider()
    {
        $originalText = file_get_contents(__DIR__ . '/_files/_inline_page_original.html');
        $expectedText = file_get_contents(__DIR__ . '/_files/_inline_page_expected.html');

        $package = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->get('Magento\Core\Model\View\DesignInterface')
            ->getDesignTheme()
            ->getPackageCode();
        $expectedText = str_replace(
            '{{design_package}}',
            $package,
            $expectedText
        );
        return array(
            'plain text'  => array('text with no translations and tags', 'text with no translations and tags'),
            'html string' => array($originalText, $expectedText),
            'html array'  => array(array($originalText), array($expectedText)),
        );
    }
}
