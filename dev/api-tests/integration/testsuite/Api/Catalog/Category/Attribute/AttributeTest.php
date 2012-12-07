<?php
/**
 * {license_notice}
 *
 * @category    Paas
 * @package     Mage_Api
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category attributes tests
 *
 * @category   Paas
 * @package    integration_tests
 * @author     Magento PaaS Team <paas-team@magento.com>
 */
class Api_Catalog_Category_Attribute_AttributeTest extends Magento_Test_Webservice
{
    /** @var SimpleXMLObject|stdClass */
    protected static $_attributeFixture;

    /** @var string */
    protected static $_code;

    /** @var array */
    protected static $_data;

    /** @var array */
    protected static $_expectedData;

    /**
     * Create new test category attribute with options
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$_attributeFixture = simplexml_load_file(dirname(__FILE__) . '/_fixture/attribute.xml');
        self::$_code = (string) self::$_attributeFixture->code;
        self::$_data = self::simpleXmlToArray(self::$_attributeFixture->attributeData);
        self::$_expectedData = self::simpleXmlToArray(self::$_attributeFixture->expected);

        $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
        $installer->addAttribute('catalog_category', self::$_code, self::$_data);
    }

    /**
     * Delete new test category attribute and its options
     *
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        $installer = new Mage_Catalog_Model_Resource_Setup('core_setup');
        $installer->removeAttribute('catalog_category', self::$_code);

        self::$_attributeFixture = null;
        self::$_code = null;
        self::$_data = null;
        self::$_expectedData = null;
    }

    /**
     * Test catalog_category_attribute.list list
     *
     * @return void
     */
    public function testList()
    {
        $attributeList = $this->call('catalog_category_attribute.list');

        $this->assertInternalType('array', $attributeList);
        $this->assertGreaterThan(0, count($attributeList));

        foreach ($attributeList as $attribute) {
            if ($attribute['code'] == self::$_code) {
                foreach (self::$_expectedData as $key => $value ) {
                    $this->assertEquals($value, $attribute[$key]);
                }
                break;
            }
        }
    }

    /**
     * Test catalog_category_attribute.options list
     *
     * @return void
     */
    public function testAttributeOptions()
    {
        $attributeOptions = $this->call('catalog_category_attribute.options', array('attributeId' => self::$_code));

        $this->assertEquals(true, is_array($attributeOptions));
        $this->assertGreaterThan(0, count($attributeOptions));

        $options = array();
        foreach ($attributeOptions as $option) {
            $options[] = $option['label'];
        }

        $this->assertEquals(self::$_data['option']['values'], $options);
    }
}
