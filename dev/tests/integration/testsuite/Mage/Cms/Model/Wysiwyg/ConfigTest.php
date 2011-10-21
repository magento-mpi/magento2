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

/**
 * @group module:Mage_Cms
 */
class Mage_Cms_Model_Wysiwyg_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Cms_Model_Wysiwyg_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Cms_Model_Wysiwyg_Config;
    }

    /**
     * Tests that config returns valid config array in it
     */
    public function testGetConfig()
    {
        $config = $this->_model->getConfig();
        $this->assertInstanceOf('Varien_Object', $config);
    }

    /**
     * Tests that config returns right urls going to static js library
     */
    public function testGetConfigJsUrls()
    {
        $config = $this->_model->getConfig();
        $this->assertStringMatchesFormat('http://localhost/js/%s', $config->getPopupCss());
        $this->assertStringMatchesFormat('http://localhost/js/%s', $config->getContentCss());
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
