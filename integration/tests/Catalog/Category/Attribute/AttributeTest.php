<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Paas
 * @package     Mage_Api
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Category attributes tests
 *
 * @category   Paas
 * @package    integration_tests
 * @author     Magento PaaS Team <paas-team@magento.com>
 */
class Catalog_Category_Attribute_AttributeTest extends Magento_Test_Webservice
{
    /** @var SimpleXMLObject|stdClass */
    protected static $_attributeFixture;

    /** @var string */
    protected static $_code;

    /** @var array */
    protected static $_data;

    /**
     * Create new test category attribute with options
     *
     * @static
     * @return void
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::$_attributeFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/attribute.xml');
        self::$_code = (string) self::$_attributeFixture->code;
        self::$_data = self::simpleXmlToArray(self::$_attributeFixture->attributeData);

        $installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');

        $installer->startSetup();
        $installer->addAttribute('catalog_category', self::$_code, self::$_data);
        $installer->endSetup();
    }

    /**
     * Delete new test category attribute and its options
     *
     * @static
     * @return void
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        $installer = new Mage_Catalog_Model_Resource_Eav_Mysql4_Setup('core_setup');

        $installer->startSetup();
        $attributeId = $installer->getAttributeId('catalog_category', self::$_code);
        $installer->removeAttribute('catalog_category', self::$_code);
        $installer->endSetup();

        // $installer->removeAttribute() doesn't delete attribute options for some reason, we must do it by hands
        $optionsCollection = new Mage_Eav_Model_Resource_Entity_Attribute_Option_Collection();
        $optionsCollection->setAttributeFilter($attributeId);

        /** @var $option Mage_Eav_Model_Entity_Attribute_Option */
        foreach ($optionsCollection->getItems() as $option) {
            $option->delete();
        }

        self::$_attributeFixture = null;
        self::$_data = null;
        self::$_code = null;
    }

    /**
     * Test catalog_category_attribute.list list
     *
     * @return void
     */
    public function testList()
    {
        $attributeList = $this->call('catalog_category_attribute.list');

        $this->assertEquals(true, is_array($attributeList));
        $this->assertEquals(true, count($attributeList) > 0);

        foreach ($attributeList as $attribute) {
            if ($attribute['code'] == self::$_code) {
                $this->assertEquals('select', $attribute['type']);
                $this->assertEquals(self::$_data['required'], $attribute['required']);
                $this->assertEquals('store', $attribute['scope']);
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
        $attributeOptions = $this->call('catalog_category_attribute.options', array(self::$_code));

        $this->assertEquals(true, is_array($attributeOptions));
        $this->assertEquals(true, count($attributeOptions) > 0);

        $options = array();
        foreach ($attributeOptions as $option) {
            if ($option['label']) {
                $options[] = $option['label'];
            }
        }

        $this->assertEquals(self::$_data['option']['values'], $options);
    }
}
