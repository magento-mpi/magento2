<?php

/**
 * Test if category changes through API properly applied on frontend
 */
class Api_Catalog_Category_FrontendTest extends Magento_Test_Webservice
{
    /** @var int Share category id between methods */
    protected static $_categoryId;

    /**
     * Fixture data
     *
     * @var array
     */
    protected $_fixture;

    /**
     * Test if category parameter is_active properly changes on frontend
     *
     * @return void
     */
    public function testCategoryUpdateAppliedOnFrontend()
    {
        $categoryFixture = $this->_getFixtureData();
        $categoryName = $categoryFixture['create']['categoryData']['name'];
        $data = $categoryFixture['create'];

        $categoryId = $this->call('category.create', $data);
        self::$_categoryId = $categoryId;

        //create
        $categoryCreated = new Mage_Catalog_Model_Category();
        $categoryCreated->load($categoryId);

        //test
        $runOptions = $this->_dispatch('customer/account/login');
        $this->assertContains($categoryName, $runOptions['response']->getBody());
        //echo $runOptions['response']->getBody();
        $this->assertEquals($categoryId,$categoryCreated->getId());
        $this->assertEquals('1', $categoryCreated['is_active']);

        //update
        $data = $categoryFixture['update'];
        $data['categoryId'] = $categoryId;
        $data['categoryData']['is_active'] = 0;
        //$data['categoryData']['name'] = $categoryName;

        $resultUpdated = $this->call('category.update', $data);

        $this->assertTrue($resultUpdated);
        $categoryUpdated = new Mage_Catalog_Model_Category();
        $categoryUpdated->load($categoryId);

        //flush helper internal cache that doesn't concern
        Mage::unregister('_helper/catalog/category');

        //test API response
        $this->assertEquals('0', $categoryUpdated['is_active']);

        //test DB
        $this->assertEquals('0', $this->_getCategory()->getIsActive());

        //test block output
        $html = $this->_getBlockOutput();
        $this->assertNotContains($categoryName, $html);

        //test page html
        $runOptions = $this->_dispatch('customer/account/login');
        $this->assertNotContains($categoryName, $runOptions['response']->getBody());
    }

    /**
     * Retrieve navigation menu block output
     * @return string
     */
    protected function _getBlockOutput()
    {
        $block = new Mage_Catalog_Block_Navigation();
        $block->setTemplate('catalog/navigation/top.phtml');
        $html = $block->toHtml();

        return $html;
    }

    /**
     * Retrieve category data
     *
     * @return Mage_Catalog_Model_Category
     */
    protected function _getCategory()
    {
        $categoryId = self::$_categoryId;

        $categoryUpdated = new Mage_Catalog_Model_Category();
        $categoryUpdated->load($categoryId);

        return $categoryUpdated;
    }

    /**
     * Get run options for controller
     *
     * @return array
     */
    protected function _getRunOptions()
    {
        /**
         * Use run options from bootstrap
         */
        $runOptions = Magento_Test_Bootstrap::getInstance()->getAppOptions();
        $runOptions['request']   = new Magento_Test_Request();
        $runOptions['response']  = new Magento_Test_Response();

        return $runOptions;
    }

    /**
     * Make dispatch
     *
     * @param string $uri
     * @return array
     */
    protected function _dispatch($uri)
    {
        $runOptions = $this->_getRunOptions();

        //Unregister previously registered controller
        Mage::unregister('controller');
        Mage::unregister('application_params');

        $urlData = @parse_url(TESTS_HTTP_HOST);
        $path = isset($urlData['path']) ? $urlData['path'] : '';
        $runOptions['request']->setRequestUri(rtrim($path, '/') . '/' . ltrim($uri, '/'));

        $runCode     = '';
        $runScope    = 'store';
        Mage::run($runCode, $runScope, $runOptions);

        return $runOptions;
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
     * Magic method which run after every test
     *
     * @return void
     */
    public function tearDown()
    {
        $categoryId = self::$_categoryId;
        $categoryDelete = $this->call('category.delete', array('categoryId' => $categoryId));

        $this->assertTrue($categoryDelete);
        $categoryCreated = new Mage_Catalog_Model_Category();
        $categoryCreated->load($categoryId);
        $this->assertEmpty($categoryCreated->getData());
    }
}
