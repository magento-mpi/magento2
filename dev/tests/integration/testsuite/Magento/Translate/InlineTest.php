<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Translate;

class InlineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Translate\Inline
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_storeId = 'default';

    /**
     * @var \Magento\Translate\Inline\StateInterface
     */
    protected $state;

    public static function setUpBeforeClass()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\State')->setAreaCode('frontend');
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\DesignInterface'
        )->setDesignTheme(
            'magento_blank'
        );
    }

    protected function setUp()
    {
        $this->_model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Translate\Inline'
        );
        $this->state = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Translate\Inline\StateInterface'
        );
        /* Called getConfig as workaround for setConfig bug */
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Core\Model\StoreManagerInterface'
        )->getStore(
            $this->_storeId
        )->getConfig(
            'dev/translate_inline/active'
        );
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Core\Model\StoreManagerInterface'
        )->getStore(
            $this->_storeId
        )->setConfig(
            'dev/translate_inline/active',
            true
        );
    }

    public function testIsAllowed()
    {
        $this->assertTrue($this->_model->isAllowed());
        $this->assertTrue($this->_model->isAllowed($this->_storeId));
        $this->assertTrue(
            $this->_model->isAllowed(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    'Magento\Core\Model\StoreManagerInterface'
                )->getStore(
                    $this->_storeId
                )
            )
        );
        $this->state->suspend();
        $this->assertFalse($this->_model->isAllowed());
        $this->assertFalse($this->_model->isAllowed($this->_storeId));
        $this->assertFalse(
            $this->_model->isAllowed(
                \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
                    'Magento\Core\Model\StoreManagerInterface'
                )->getStore(
                    $this->_storeId
                )
            )
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
        $this->_model->processResponseBody($actualText, false);
        $this->markTestIncomplete('Bug MAGE-2494');

        $expected = new \DOMDocument();
        $expected->preserveWhiteSpace = false;
        $expected->loadHTML($expectedText);

        $actual = new \DOMDocument();
        $actual->preserveWhiteSpace = false;
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

        $package = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\View\DesignInterface'
        )->getDesignTheme()->getPackageCode();
        $expectedText = str_replace('{{design_package}}', $package, $expectedText);
        return array(
            'plain text' => array('text with no translations and tags', 'text with no translations and tags'),
            'html string' => array($originalText, $expectedText),
            'html array' => array(array($originalText), array($expectedText))
        );
    }
}
