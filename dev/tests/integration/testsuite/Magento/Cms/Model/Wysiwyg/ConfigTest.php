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

/**
 * @magentoAppArea adminhtml
 */
namespace Magento\Cms\Model\Wysiwyg;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
    protected $_model;

    protected function setUp()
    {
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\Config\Scope')
            ->setCurrentScope(\Magento\Core\Model\App\Area::AREA_ADMINHTML);
        $this->_model = \Mage::getModel('Magento\Cms\Model\Wysiwyg\Config');
    }

    /**
     * Tests that config returns valid config array in it
     */
    public function testGetConfig()
    {
        $config = $this->_model->getConfig();
        $this->assertInstanceOf('Magento\Object', $config);
    }

    /**
     * Tests that config returns right urls going to static js library
     */
    public function testGetConfigJsUrls()
    {
        $config = $this->_model->getConfig();
        $this->assertStringMatchesFormat('http://localhost/pub/lib/%s', $config->getPopupCss());
        $this->assertStringMatchesFormat('http://localhost/pub/lib/%s', $config->getContentCss());
    }

    /**
     * Tests that config doesn't process incoming already prepared data
     *
     * @dataProvider getConfigNoProcessingDataProvider
     */
    public function testGetConfigNoProcessing($original)
    {
        $config = $this->_model->getConfig($original);
        $actual = $config->getData();
        foreach (array_keys($actual) as $key) {
            if (!isset($original[$key])) {
                unset($actual[$key]);
            }
        }
        $this->assertEquals($original, $actual);
    }

    /**
     * @return array
     */
    public function getConfigNoProcessingDataProvider()
    {
        return array(
            array(
                array(
                    'files_browser_window_url'      => 'http://example.com/111/',
                    'directives_url'                => 'http://example.com/222/',
                    'popup_css'                     => 'http://example.com/333/popup.css',
                    'content_css'                   => 'http://example.com/444/content.css',
                    'directives_url_quoted'         => 'http://example.com/555/'
                )
            ),
            array(
                array(
                    'files_browser_window_url'      => '/111/',
                    'directives_url'                => '/222/',
                    'popup_css'                     => '/333/popup.css',
                    'content_css'                   => '/444/content.css',
                    'directives_url_quoted'         => '/555/'
                )
            ),
            array(
                array(
                    'files_browser_window_url'      => '111/',
                    'directives_url'                => '222/',
                    'popup_css'                     => '333/popup.css',
                    'content_css'                   => '444/content.css',
                    'directives_url_quoted'         => '555/'
                )
            )
        );
    }
}
