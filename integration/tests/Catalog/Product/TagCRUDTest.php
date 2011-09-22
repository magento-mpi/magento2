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
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @magentoDataFixture Catalog/Product/_fixtures/TagCRUD.php
 */
class Catalog_Product_TagCRUDTest extends Magento_Test_Webservice
{
    /**
     * Test tag CRUD
     *
     * @return void
     */
    public function testTagCRUD()
    {
        $tagFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/xml/TagCRUD.xml');
        $data = self::simpleXmlToArray($tagFixture->tagData);
        $expected = self::simpleXmlToArray($tagFixture->expected);

        $data['product_id'] = Magento_Test_Webservice::getFixture('productData')->getId();
        $data['customer_id'] = Magento_Test_Webservice::getFixture('customerData')->getId();

        // create test
        $createdTagId = $this->call('product_tag.add', array($data));
        $this->assertGreaterThan(0, $createdTagId);

        // Invalid product ID exception test
        try {
            $data['product_id'] = mt_rand(10000, 99999);
            $this->call('product_tag.add', array($data));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

        // Invalid customer ID exception test
        try {
            $data['product_id'] = Magento_Test_Webservice::getFixture('productData')->getId();
            $data['customer_id'] = mt_rand(10000, 99999);
            $this->call('product_tag.add', array($data));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

        // Invalid store ID exception test
        try {
            $data['product_id'] = Magento_Test_Webservice::getFixture('productData')->getId();
            $data['customer_id'] = Magento_Test_Webservice::getFixture('customerData')->getId();
            $data['store'] = mt_rand(10000, 99999);
            $this->call('product_tag.add', array($data));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) { }

        // items list test
        $tagsList = $this->call('product_tag.list', array(
            Magento_Test_Webservice::getFixture('productData')->getId()
        ));
        $this->assertNotEmpty($tagsList, "Can't find added tag in list");
        $this->assertEquals($expected['created_tags_count'], count($tagsList), "Can't find added tag in list");

        // delete test
        $tagToDelete = array_shift($tagsList);
        $tagDelete = $this->call('product_tag.remove', array($tagToDelete['tag_id']));
        $this->assertTrue($tagDelete, "Can't delete added tag");

        // Delete exception test
        $this->setExpectedException('Exception');
        $this->call('product_tag.remove', array($tagToDelete['tag_id']));
    }
}
