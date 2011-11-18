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

/**
 * @group module:Mage_Customer
 */
class Mage_Customer_Model_Address_ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Customer_Model_Address_Config
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= new Mage_Customer_Model_Address_Config();
    }

    /**
     * @magentoDataFixture Mage/Customer/_files/address_formats.php
     */
    public function testGetFormats()
    {
        $original = array(
            'escaped_one' => true,
            'escaped_two' => false,
            'escaped_three' => false,
            'escaped_four' => false,
            'escaped_five' => false,
            'escaped_six' => true
        );

        $formats = $this->_model->getFormats();
        $storeId = $this->_model->getStore()->getId();
        $this->assertNotEmpty($formats[$storeId]);
        $foundFormats = 0;
        $lastFormat = null;
        foreach ($formats as $format) {
            if (isset($original[$format->getCode()])) {
                $this->assertEquals($original[$format->getCode()], $format->getEscapeHtml());
                $lastFormat = $format;
                ++$foundFormats;
            }
        }

        $this->assertEquals(count($original), $foundFormats);

        $this->assertInstanceOf(
            Mage_Customer_Model_Address_Config::DEFAULT_ADDRESS_RENDERER,
            $lastFormat->getRenderer()
        );
    }
}
