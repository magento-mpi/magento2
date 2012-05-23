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
 * Test API category methods
 *
 * @category    Catalog
 * @package     Catalog_Category
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api_Catalog_Category_CategoryTest extends Magento_Test_Webservice
{
    /**
     * Fixture data
     *
     * @var array
     */
    protected $_fixture;

    /**
     * Get formatter design date
     *
     * @param string $date
     * @return string
     */
    protected function _formatActiveDesignDate($date)
    {
        list($m, $d, $y) = explode('/', $date);
        return "$y-$m-$d 00:00:00";
    }

    /**
     * Get fixture data
     *
     * @return array
     */
    protected function _getFixtureData()
    {
        if (null === $this->_fixture) {
            $this->_fixture = require dirname(__FILE__) . '/_fixtures/categoryData.php';
        }
        return $this->_fixture;
    }

    /**
     * Test category CRUD
     */
    public function testCategoryCrud()
    {
        $categoryFixture = $this->_getFixtureData();
        $categoryId = $this->call('category.create', $categoryFixture['create']);

        $this->assertEquals($categoryId, (int) $categoryId,
            'Result of a create method is not an integer.');
        /**
         * Test create
         */
        $category = new Mage_Catalog_Model_Category();
        $category->load($categoryId);

        try {
            //check created data
            $this->assertEquals($categoryId,
                $category->getId(),
                'Category ID is not same like from API result.');

            $this->assertEquals(
                $category['custom_design_from'],
                $this->_formatActiveDesignDate(
                    $categoryFixture['create']['categoryData']['custom_design_from']),
                'Category active design date is not the same like sent to API on create.');

            $this->assertEquals(
                $category['custom_design_to'],
                $this->_formatActiveDesignDate(
                    $categoryFixture['create']['categoryData']['custom_design_to']),
                'Category active design date is not the same like sent to API on create.');

            $this->assertNotEmpty($category['position'],
                'Category position is empty.');
            $this->assertFalse(array_key_exists('custom_design_apply', $category->getData()),
                'Category data item "custom_design_apply" is deprecated.');

            foreach ($categoryFixture['create']['categoryData'] as $name => $value) {
                if (in_array($name, $categoryFixture['create_skip_to_check'])) {
                    continue;
                }
                $this->assertEquals($value, $category[$name],
                    sprintf('Category "%s" is "%s" and not the same like sent to create "%s".',
                            $name,
                        $category[$name], $value)
                );
            }

            /**
             * Test update
             */
            $categoryFixture['update']['categoryId'] = $categoryId;
            $resultUpdated = $this->call('category.update', $categoryFixture['update']);
            $this->assertTrue($resultUpdated);

            $category = new Mage_Catalog_Model_Category();
            $category->load($categoryId);

            //check updated data
            $this->assertEquals(
                $category['custom_design_from'],
                $this->_formatActiveDesignDate(
                    $categoryFixture['update']['categoryData']['custom_design_from']),
                'Category active design date is not the same like sent to API on update.');

            foreach ($categoryFixture['update']['categoryData'] as $name => $value) {
                if (in_array($name, $categoryFixture['update_skip_to_check'])) {
                    continue;
                }
                $this->assertEquals($value, $category[$name],
                    sprintf('Category data with name "%s" is not the same like sent to update.', $name));
            }

            /**
             * Test read
             */
            $categoryRead = $this->call(
                'catalog_category.info', array('categoryId' => $categoryId, $categoryFixture['update']['storeView'])
            );

            $this->assertEquals(
                $categoryRead['custom_design_from'],
                $this->_formatActiveDesignDate(
                    $categoryFixture['update']['categoryData']['custom_design_from']),
                'Category active design date is not the same like sent to API on update.');

            $this->assertFalse(array_key_exists('custom_design_apply', $categoryRead),
                            'Category data item "custom_design_apply" is deprecated.');

            foreach ($categoryFixture['update']['categoryData'] as $name => $value) {
                if (in_array($name, $categoryFixture['update_skip_to_check'])) {
                    continue;
                }
                $this->assertEquals($value, $categoryRead[$name],
                    sprintf('Category data with name "%s" is not the same like sent to update.', $name));
            }

            /**
             * Test delete
             */
            $categoryDelete = $this->call('category.delete', array('categoryId' => $categoryId));
            $this->assertTrue($categoryDelete);

            $category = new Mage_Catalog_Model_Category();
            $category->load($categoryId);
            $this->assertEmpty($category->getId());
        } catch (Exception $e) {
            //delete created category
            $this->callModelDelete($category, true);
            throw $e;
        }
    }

    /**
     * Test category bad request
     *
     * Test fault requests and vulnerability requests
     */
    public function testCategoryBadRequest()
    {
        $categoryFixture = $this->_getFixtureData();
        $params = $categoryFixture['create'];

        /**
         * Test vulnerability SQL injection in is_active
         */
        $params['categoryData']['is_active'] = $categoryFixture['vulnerability']['categoryData']['is_active'];

        $categoryId = $this->call('category.create', $params);
        $this->assertEquals($categoryId, (int) $categoryId,
            'Category cannot created with vulnerability in is_active field'
        );

        $category = new Mage_Catalog_Model_Category();
        $category->load($categoryId);

        try {
            $this->assertEquals($category['is_active'],
                (int) $categoryFixture['vulnerability']['categoryData']['is_active']);

            /**
             * Test update with empty category ID
             */
            $params = $categoryFixture['update'];
            $params['categoryId'] = 'invalid_category_id';
            try {
                $result = $this->call('category.update', $params);
            } catch (SoapFault $e) {
                //make result like in response
                $result = array(
                    'faultcode' => $e->faultcode,
                    'faultstring' => $e->faultstring
                );
            } catch (Zend_XmlRpc_Client_FaultException $e) {
                //make result like in response
                $result = array(
                    'faultcode' => $e->getCode(),
                    'faultstring' => $e->getMessage()
                );
            }

            $category->load($categoryId);
            //name must has old value
            $this->assertEquals(
                $category['name'],
                $categoryFixture['create']['categoryData']['name'],
                'Category updated with empty ID.'
            );
            //"102" is code error when category is not found on update
            $this->assertInternalType('array', $result);
            $this->assertEquals(102, $result['faultcode'], 'Fault code is not right.');

            /**
             * Test vulnerability with helper usage in custom layout update
             */
            $params['categoryId'] = $categoryId;
            $params['categoryData']['custom_layout_update'] =
                    $categoryFixture['vulnerability']['categoryData']['custom_layout_update'];
            try {
                $result = $this->call('category.update', $params);
            } catch (SoapFault $e) {
                //make result like in response
                $result = array(
                    'faultcode' => $e->faultcode,
                    'faultstring' => $e->faultstring
                );
            } catch (Zend_XmlRpc_Client_FaultException $e) {
                //make result like in response
                $result = array(
                    'faultcode' => $e->getCode(),
                    'faultstring' => $e->getMessage()
                );
            }
            $category->load($categoryId);

            //"103" is code error when data validation is not passed
            $this->assertInternalType('array', $result);
            $this->assertEquals(103, $result['faultcode'], 'Fault code is not right.');
        } catch (Exception $e) {
            //delete created category
            $this->callModelDelete($category, true);
            throw $e;
        }

        $this->callModelDelete($category, true);
    }

    /**
     * Test delete root category
     */
    public function testRootCategoryDelete()
    {
        try {
            $result = $this->call('category.delete', array('categoryId' => Mage_Catalog_Model_Category::TREE_ROOT_ID));
        } catch (SoapFault $e) {
            $result = array(
                'faultcode' => $e->faultcode,
                'faultstring' => $e->faultstring
            );
        } catch (Zend_XmlRpc_Client_FaultException $e) {
            $result = array(
                'faultcode' => $e->getCode(),
                'faultstring' => $e->getMessage()
            );
        }

        $this->assertInternalType('array', $result);
        $this->assertEquals(105, $result['faultcode'], 'Fault code is not right.');
        $this->assertEquals('Cannot remove the system category.', $result['faultstring'],
            'Exception message is not right.');

        $category = new Mage_Catalog_Model_Category();
        $this->assertNotNull($category->load(Mage_Catalog_Model_Category::TREE_ROOT_ID)->getId());
    }
}
