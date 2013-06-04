<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for related, up-sell and cross-sell products.
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Product_Linking_SimpleLinkingTest extends Mage_Selenium_TestCase
{
    protected $_productTypes = array('grouped', 'configurable', 'bundle', 'simple', 'virtual', 'downloadable');

    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->runMassAction('Delete', 'all');
    }

    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Create all types of products</p>
     *
     * @return array
     * @test
     */
    public function preconditionsForTests()
    {
        $forLinking = array();
        $linking = $this->productHelper()->createSimpleProduct();
        foreach ($this->_productTypes as $product) {
            $method = 'create' . ucfirst($product) . 'Product';
            $forLinking[$product] = $this->productHelper()->$method();
        }

        return array($linking, $forLinking);
    }

    /**
     * <p>Review Related products(inStock) on frontend assigned to simple product.</p>
     *
     * @param array $testData
     * @param string $linkingType
     *
     * @test
     * @dataProvider linkingTypeDataProvider
     * @depends preconditionsForTests
     */
    public function relatedInStock($linkingType, $testData)
    {
        //Data
        $assignType = 'related';
        $assignProductType = 'simple';
        list($linking, $forLinking) = $testData;
        $forLinking = $forLinking[$linkingType][$linkingType];
        $search = $this->loadDataSet('Product', 'product_search', $linking[$assignProductType]);
        $assign = $this->loadDataSet('Product', $assignType . '_1',
                                     array($assignType . '_search_name' => $forLinking['product_name'],
                                          $assignType . '_search_sku'   => $forLinking['product_sku']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->unselectAssociatedProduct($assignType);
        $this->productHelper()->assignProduct($assign, $assignType);
        $this->productHelper()->saveProduct('continueEdit');
        $this->productHelper()->isAssignedProduct($assign, $assignType);
        $this->assertEmptyVerificationErrors();
        $this->clearInvalidedCache();
        $this->reindexInvalidedData();
        $this->productHelper()->frontOpenProduct($linking[$assignProductType]['product_name']);
        $this->addParameter('productName', $forLinking['product_name']);
        if (!$this->controlIsPresent('link', $assignType . '_product')) {
            $this->addVerificationMessage($assignType . ' product ' . $forLinking['product_name']
                                              . ' is not on "' . $this->getCurrentPage() . '" page');
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Review Cross-sell products(inStock) on frontend assigned to simple product.</p>
     *
     * @param array $testData
     * @param string $linkingType
     *
     * @test
     * @dataProvider linkingTypeDataProvider
     * @depends preconditionsForTests
     */
    public function crossSellsInStock($linkingType, $testData)
    {
        //Data
        $assignType = 'cross_sells';
        $assignProductType = 'simple';
        list($linking, $forLinking) = $testData;
        $forLinking = $forLinking[$linkingType][$linkingType];
        $search = $this->loadDataSet('Product', 'product_search', $linking[$assignProductType]);
        $assign = $this->loadDataSet('Product', $assignType . '_1',
                                     array($assignType . '_search_name' => $forLinking['product_name'],
                                          $assignType . '_search_sku'   => $forLinking['product_sku']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->unselectAssociatedProduct($assignType);
        $this->productHelper()->assignProduct($assign, $assignType);
        $this->productHelper()->saveProduct('continueEdit');
        $this->productHelper()->isAssignedProduct($assign, $assignType);
        $this->assertEmptyVerificationErrors();
        $this->clearInvalidedCache();
        $this->reindexInvalidedData();
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->productHelper()->frontOpenProduct($linking[$assignProductType]['product_name']);
        $this->productHelper()->frontAddProductToCart();
        $this->addParameter('productName', $forLinking['product_name']);
        if (!$this->controlIsPresent('link', $assignType . '_product')) {
            $this->addVerificationMessage($assignType . ' product ' . $forLinking['product_name']
                                              . ' is not on "' . $this->getCurrentPage() . '" page');
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Review Up-sell products(inStock) on frontend assigned to simple product.</p>
     *
     * @param array $testData
     * @param string $linkingType
     *
     * @test
     * @dataProvider linkingTypeDataProvider
     * @depends preconditionsForTests
     */
    public function upSellsInStock($linkingType, $testData)
    {
        //Data
        $assignType = 'up_sells';
        $assignProductType = 'simple';
        list($linking, $forLinking) = $testData;
        $forLinking = $forLinking[$linkingType][$linkingType];
        $search = $this->loadDataSet('Product', 'product_search', $linking[$assignProductType]);
        $assign = $this->loadDataSet('Product', $assignType . '_1',
                                     array($assignType . '_search_name' => $forLinking['product_name'],
                                          $assignType . '_search_sku'   => $forLinking['product_sku']));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->unselectAssociatedProduct($assignType);
        $this->productHelper()->assignProduct($assign, $assignType);
        $this->productHelper()->saveProduct('continueEdit');
        $this->productHelper()->isAssignedProduct($assign, $assignType);
        $this->assertEmptyVerificationErrors();
        $this->clearInvalidedCache();
        $this->reindexInvalidedData();
        $this->productHelper()->frontOpenProduct($linking[$assignProductType]['product_name']);
        $this->addParameter('productName', $forLinking['product_name']);
        if (!$this->controlIsPresent('link', $assignType . '_product')) {
            $this->addVerificationMessage($assignType . ' product ' . $forLinking['product_name']
                                              . ' is not on "' . $this->getCurrentPage() . '" page');
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * <p>Review Related products(OutStock) on frontend assigned to simple product.</p>
     *
     * @param array $testData
     * @param string $linkingType
     *
     * @test
     * @dataProvider linkingTypeDataProvider
     * @depends preconditionsForTests
     */
    public function relatedOutStock($linkingType, $testData)
    {
        //Data
        $assignType = 'related';
        $assignProductType = 'simple';
        list($linking, $forLinking) = $testData;
        $forLinking = $forLinking[$linkingType][$linkingType];
        $search = $this->loadDataSet('Product', 'product_search', $linking[$assignProductType]);
        $assign = $this->loadDataSet('Product', $assignType . '_1',
                                     array($assignType . '_search_name' => $forLinking['product_name'],
                                          $assignType . '_search_sku'   => $forLinking['product_sku']));
        $searchAssigned = $this->loadDataSet('Product', 'product_search', $forLinking);
        //Steps
        $this->navigate('manage_products');
        //Set product to 'Out of Stock';
        $this->productHelper()->openProduct($searchAssigned);
        $this->productHelper()->openProductTab('inventory');
        $this->fillDropdown('inventory_stock_availability', 'Out of Stock');
        $this->productHelper()->saveProduct('continueEdit');
        //Assign product
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->unselectAssociatedProduct($assignType);
        $this->productHelper()->assignProduct($assign, $assignType);
        $this->productHelper()->saveProduct('continueEdit');
        $this->productHelper()->isAssignedProduct($assign, $assignType);
        $this->assertEmptyVerificationErrors();
        $this->clearInvalidedCache();
        $this->reindexInvalidedData();
        //Verify
        $this->productHelper()->frontOpenProduct($linking[$assignProductType]['product_name']);
        $this->addParameter('productName', $forLinking['product_name']);
        if ($this->controlIsPresent('link', $assignType . '_product')) {
            $this->addVerificationMessage($assignType . ' product ' . $forLinking['product_name']
                                              . ' is on "' . $this->getCurrentPage() . '" page');
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Review Cross-sell products(OutStock) on frontend assigned to simple product.</p>
     *
     * @param array $testData
     * @param string $linkingType
     *
     * @test
     * @dataProvider linkingTypeDataProvider
     * @depends preconditionsForTests
     */
    public function crossSellsOutStock($linkingType, $testData)
    {
        //Data
        $assignType = 'cross_sells';
        $assignProductType = 'simple';
        list($linking, $forLinking) = $testData;
        $forLinking = $forLinking[$linkingType][$linkingType];
        $search = $this->loadDataSet('Product', 'product_search', $linking[$assignProductType]);
        $assign = $this->loadDataSet('Product', $assignType . '_1',
                                     array($assignType . '_search_name' => $forLinking['product_name'],
                                          $assignType . '_search_sku'   => $forLinking['product_sku']));
        //Steps
        $searchAssigned = $this->loadDataSet('Product', 'product_search', $forLinking);
        //Steps
        $this->navigate('manage_products');
        //Set product to 'Out of Stock';
        $this->productHelper()->openProduct($searchAssigned);
        $this->productHelper()->openProductTab('inventory');
        $this->fillDropdown('inventory_stock_availability', 'Out of Stock');
        $this->productHelper()->saveProduct('continueEdit');
        //Assign product
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->unselectAssociatedProduct($assignType);
        $this->productHelper()->assignProduct($assign, $assignType);
        $this->productHelper()->saveProduct('continueEdit');
        $this->productHelper()->isAssignedProduct($assign, $assignType);
        $this->assertEmptyVerificationErrors();
        $this->clearInvalidedCache();
        $this->reindexInvalidedData();
        $this->frontend();
        $this->shoppingCartHelper()->frontClearShoppingCart();
        $this->productHelper()->frontOpenProduct($linking[$assignProductType]['product_name']);
        $this->productHelper()->frontAddProductToCart();
        $this->addParameter('productName', $forLinking['product_name']);
        if ($this->controlIsPresent('link', $assignType . '_product')) {
            $this->addVerificationMessage($assignType . ' product ' . $forLinking['product_name']
                                              . ' is on "' . $this->getCurrentPage() . '" page');
        }
        $this->assertEmptyVerificationErrors();
    }

    /**
     * Review Up-sell products(OutStock) on frontend assigned to simple product.</p>
     *
     * @param array $testData
     * @param string $linkingType
     *
     * @test
     * @dataProvider linkingTypeDataProvider
     * @depends preconditionsForTests
     */
    public function upSellsOutStock($linkingType, $testData)
    {
        //Data
        $assignType = 'up_sells';
        $assignProductType = 'simple';
        list($linking, $forLinking) = $testData;
        $forLinking = $forLinking[$linkingType][$linkingType];
        $search = $this->loadDataSet('Product', 'product_search', $linking[$assignProductType]);
        $assign = $this->loadDataSet('Product', $assignType . '_1',
                                     array($assignType . '_search_name' => $forLinking['product_name'],
                                          $assignType . '_search_sku'   => $forLinking['product_sku']));
        //Steps
        $searchAssigned = $this->loadDataSet('Product', 'product_search', $forLinking);
        //Steps
        $this->navigate('manage_products');
        //Set product to 'Out of Stock';
        $this->productHelper()->openProduct($searchAssigned);
        $this->productHelper()->openProductTab('inventory');
        $this->fillDropdown('inventory_stock_availability', 'Out of Stock');
        $this->productHelper()->saveProduct('continueEdit');
        //Assign product
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($search);
        $this->productHelper()->unselectAssociatedProduct($assignType);
        $this->productHelper()->assignProduct($assign, $assignType);
        $this->productHelper()->saveProduct('continueEdit');
        $this->productHelper()->isAssignedProduct($assign, $assignType);
        $this->assertEmptyVerificationErrors();
        $this->clearInvalidedCache();
        $this->reindexInvalidedData();
        $this->productHelper()->frontOpenProduct($linking[$assignProductType]['product_name']);
        $this->addParameter('productName', $forLinking['product_name']);
        if ($this->controlIsPresent('link', $assignType . '_product')) {
            $this->addVerificationMessage($assignType . ' product ' . $forLinking['product_name']
                                              . ' is on "' . $this->getCurrentPage() . '" page');
        }
        $this->assertEmptyVerificationErrors();
    }

    public function linkingTypeDataProvider()
    {
        return array(
            array('simple'),
            array('virtual'),
            array('downloadable'),
            array('bundle'),
            array('configurable'),
            array('grouped')
        );
    }
}