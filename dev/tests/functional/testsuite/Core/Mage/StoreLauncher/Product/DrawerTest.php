<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_StoreLauncher
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product Drawer tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_StoreLauncher_Product_DrawerTest extends Mage_Selenium_TestCase
{
    /**
     * Store new window handle
     *
     * @var null
     */
    private $_windowId = null;

    /**
     * <p>Preconditions:</p>
     * <p>1. Login to Backend</p>
     * <p>1.1 Remove products if any. Set tile state to initial</p>
     * <p>2. Navigate to Store Launcher page</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $tileState = $this->getControlAttribute('fieldset', 'product_tile', 'class');
        $changeState = ('tile-store-settings tile-product tile-complete' == $tileState) ? true : false;
        if ($changeState) {
            $this->storeLauncherHelper()->setTileState('product', Core_Mage_StoreLauncher_Helper::$STATE_TODO);
            //Remove all products
            $this->navigate('manage_products');
            $this->runMassAction('Delete', 'all');
            $this->admin();
        }
    }

    /**
     * <p>Close additional browser window<p>
     */
    protected function tearDownAfterTest()
    {
        //Close 'New' browser window if any
        if ($this->_windowId) {
            $this->closeWindow($this->_windowId);
            $this->_windowId = null;
        }
        //Back to main window
        $this->window('');
    }

    /**
     * <p>User can navigate to pages where he can import product(s)</p>
     *
     * @dataProvider navigateToPagesDataProvider
     * @TestlinkId TL-MAGE-6823
     * @test
     */
    public function navigateToPages($link, $page)
    {
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('product_tile');
        $this->assertTrue($this->controlIsPresent(self::FIELD_TYPE_LINK, $link),
            'There is no ' . $link . ' on Product Drawer.');
        $this->clickControl(self::FIELD_TYPE_LINK, $link);
        //Switch to new window
        $this->_windowId = $this->selectLastWindow();
        $this->validatePage($page);
    }

    /**
     * Data for navigateToPages
     *
     * @return array
     */
    public function navigateToPagesDataProvider()
    {
        return array(
            array('categories', 'manage_categories'),
            array('export', 'export'),
            array('import', 'import'),
        );
    }

    /**
     * <p>User can save product on Product Tile</p>
     *
     * @TestlinkId TL-MAGE-6824
     * @test
     */
    public function createProductFromTile()
    {
        //Data
        $productData = $this->loadDataSet('ProductTile', 'simple_product');
        $productSearch =
            $this->loadDataSet('Product', 'product_search', array('product_sku' => $productData['general_sku']));
        $category = $this->loadDataSet('ProductTile', 'default_category');
        /**
         * @var Core_Mage_StoreLauncher_Helper $helper
         */
        $this->assertEquals('tile-store-settings tile-product tile-todo',
            $this->getControlAttribute('fieldset', 'product_tile', 'class'), 'Tile state is not Equal to TODO');
        $helper = $this->storeLauncherHelper();
        $helper->openDrawer('product_tile');
        $this->fillFieldset($productData, 'product_drawer_form');
        $this->productHelper()->selectProductCategories($category['general_categories']);
        $helper->saveDrawer();
        //Verifying
        $this->assertEquals('tile-store-settings tile-product tile-complete',
            $this->getControlAttribute('fieldset', 'product_tile', 'class'), 'Tile state is not Equal to Complete');
        $this->navigate('manage_products');
        $this->productHelper()->openProduct($productSearch);
        $productData = array_merge($productData, $category);
        $this->productHelper()->verifyProductInfo($productData);
    }
}