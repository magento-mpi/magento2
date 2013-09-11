<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Customer_Model_Address_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= Mage::getModel('Magento\Customer\Model\Address\Config');
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Customer/_files/address_formats.php
     */
    public function testGetFormats()
    {
        $expectedFormatEscape = array(
            'escaped_one' => true,
            'escaped_two' => false,
            'escaped_three' => false,
            'escaped_four' => false,
            'escaped_five' => false,
            'escaped_six' => true
        );

        $formats = array();
        foreach ($this->_model->getFormats() as $format) {
            $formats[$format->getCode()] = $format;
        }

        foreach ($expectedFormatEscape as $formatCode => $escapeHtml) {
            if (isset($formats[$formatCode])) {
                $format = $formats[$formatCode];
                $this->assertEquals($escapeHtml, $format->getEscapeHtml());
            } else {
                $this->fail("Missing '{$formatCode}' item in the fixture.");
            }

            $this->assertInstanceOf(
                \Magento\Customer\Model\Address\Config::DEFAULT_ADDRESS_RENDERER,
                $format->getRenderer()
            );
        }
    }
}
