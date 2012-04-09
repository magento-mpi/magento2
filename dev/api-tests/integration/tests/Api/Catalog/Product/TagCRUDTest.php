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
 * @magentoDataFixture Api/Catalog/Product/_fixtures/TagCRUD.php
 */
class Api_Catalog_Product_TagCRUDTest extends Magento_Test_Webservice
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
        $createdTags = $this->call('product_tag.add', array('data' => $data));

        $this->assertCount(3, $createdTags);

        // Invalid product ID exception test
        try {
            $data['product_id'] = mt_rand(10000, 99999);
            $this->call('product_tag.add', array('data' => $data));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) {
            $this->assertEquals('Requested product does not exist.', $e->getMessage());
        }

        // Invalid customer ID exception test
        try {
            $data['product_id'] = Magento_Test_Webservice::getFixture('productData')->getId();
            $data['customer_id'] = mt_rand(10000, 99999);
            $this->call('product_tag.add', array('data' => $data));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) {
            $this->assertEquals('Requested customer does not exist.', $e->getMessage());
        }

        // Invalid store ID exception test
        try {
            $data['product_id'] = Magento_Test_Webservice::getFixture('productData')->getId();
            $data['customer_id'] = Magento_Test_Webservice::getFixture('customerData')->getId();
            $data['store'] = mt_rand(10000, 99999);
            $this->call('product_tag.add', array('data' => $data));
            $this->fail("Didn't receive exception!");
        } catch (Exception $e) {
            $this->assertEquals('Requested store does not exist.', $e->getMessage());
        }

        // items list test
        $tagsList = $this->call('product_tag.list', array(
            'productId' => Magento_Test_Webservice::getFixture('productData')->getId(), 'store' => 0
        ));
        $this->assertInternalType('array', $tagsList);
        $this->assertNotEmpty($tagsList, "Can't find added tag in list");
        $this->assertCount((int) $expected['created_tags_count'], $tagsList, "Can't find added tag in list");

        // delete test
        $tagToDelete = array_shift($tagsList);
        $tagDelete = $this->call('product_tag.remove', array('tagId' => $tagToDelete['tag_id']));
        $this->assertTrue((bool) $tagDelete, "Can't delete added tag");

        // Delete exception test
        $this->setExpectedException(self::DEFAULT_EXCEPTION, 'Requested tag does not exist.');
        $this->call('product_tag.remove', array('tagId' => $tagToDelete['tag_id']));
    }
}
