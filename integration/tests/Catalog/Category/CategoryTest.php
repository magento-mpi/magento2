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
 * @ magentoDataFixture Catalog/Category/_fixtures/category.php
 */
class Catalog_Category_CategoryTest extends Magento_Test_Webservice
{
    public function tearDown()
    {
        parent::tearDown();
    }

    public function testCategoryCRUD()
    {
        $categoryFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/category.xml');
        $data = self::simpleXmlToArray($categoryFixture->create);

        $categoryId = $this->call('category.create', $data);

        //create
        $categoryCreated = new Mage_Catalog_Model_Category();
        $categoryCreated->load($categoryId);

        $this->assertEquals($categoryId,$categoryCreated->getId());
        $this->assertEquals('Category 1.1', $categoryCreated['name']);

        //update
        $categoryFixture->update->categoryId = $categoryId;
        $data = self::simpleXmlToArray($categoryFixture->update);

        $resultUpdated = $this->call('category.update', $data);

        $this->assertTrue($resultUpdated);
        $categoryUpdated = new Mage_Catalog_Model_Category();
        $categoryUpdated->load($categoryId);

        $this->assertEquals('Category 1.1 Updated', $categoryUpdated['name']);

        //read
        $categoryRead = $this->call('catalog_category.info', array($categoryId));
        $this->assertEquals('Category 1.1 Updated', $categoryRead['name']);

        //delete
        $categoryDelete = $this->call('category.delete', array($categoryId));

        $this->assertTrue($categoryDelete);
        $categoryCreated = new Mage_Catalog_Model_Category();
        $categoryCreated->load($categoryId);
        $this->assertEmpty($categoryCreated->getData());
    }

    public function testCategoryUpdateAppliedOnFrontend()
    {
        $categoryFixture = simplexml_load_file(dirname(__FILE__) . '/_fixtures/category.xml');
        $data = self::simpleXmlToArray($categoryFixture->create);

        $categoryId = $this->call('category.create', $data);

        //create
        $categoryCreated = new Mage_Catalog_Model_Category();
        $categoryCreated->load($categoryId);

        $this->assertEquals($categoryId,$categoryCreated->getId());
        $this->assertEquals('1', $categoryCreated['is_active']);

        //update
        $categoryFixture->update->categoryId = $categoryId;
        $categoryFixture->update->categoryData->is_active = '0';
        $data = self::simpleXmlToArray($categoryFixture->update);

        $resultUpdated = $this->call('category.update', $data);

        $this->assertTrue($resultUpdated);
        $categoryUpdated = new Mage_Catalog_Model_Category();
        $categoryUpdated->load($categoryId);

        $this->assertEquals('Category 1.1 Updated', $categoryUpdated['name']);
        $this->assertEquals('0', $categoryUpdated['is_active']);

        //read
        $categoryRead = $this->call('catalog_category.info', array($categoryId));
        $this->assertEquals('0', $categoryRead['is_active']);

        //update empty
        $categoryFixture->update->categoryId = $categoryId;
        $categoryFixture->update->categoryData->is_active = '';
        $data = self::simpleXmlToArray($categoryFixture->update);

        try {
            $this->call('category.update', $data);
            $this->fail('Exception not thrown');
        } catch (SoapFault $e) {
            //correct behavior
        } catch (Exception $e) {
            $this->fail('Wrong exception thrown');
        }

        //update sql-injection
        $categoryFixture->update->categoryId = $categoryId;
        $categoryFixture->update->categoryData->is_active = '9-1';
        $data = self::simpleXmlToArray($categoryFixture->update);

        $resultUpdated = $this->call('category.update', $data);

        $this->assertTrue($resultUpdated);
        $categoryUpdated = new Mage_Catalog_Model_Category();
        $categoryUpdated->load($categoryId);

        $this->assertEquals('9', $categoryUpdated['is_active']);

        //read sql-injection
        $categoryRead = $this->call('catalog_category.info', array($categoryId));
        $this->assertEquals('9', $categoryRead['is_active']);

        //delete
        $categoryDelete = $this->call('category.delete', array($categoryId));

        $this->assertTrue($categoryDelete);
        $categoryCreated = new Mage_Catalog_Model_Category();
        $categoryCreated->load($categoryId);
        $this->assertEmpty($categoryCreated->getData());
    }

    public function _testCategoryUpdateRealCategory()
    {
        $categoryId = 55;
        $isActive = '1';

        $data = array(
            'categoryId'    => $categoryId,
            'categoryData'  => array(
                'is_active' => $isActive,
                'name'      => 'sub category 01',
                'default_sort_by'   => 'name',
                'available_sort_by' => array(
                    'sort_by'   => 'name'
                )
            )
        );
        $resultUpdated = $this->call('category.update', $data);
        $this->assertTrue($resultUpdated);

        $categoryUpdated = new Mage_Catalog_Model_Category();
        $categoryUpdated->load($categoryId);

        $this->assertEquals($isActive, $categoryUpdated['is_active']);
    }
}
