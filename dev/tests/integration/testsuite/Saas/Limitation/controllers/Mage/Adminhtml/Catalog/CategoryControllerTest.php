<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Saas_Limitation_Mage_Adminhtml_Catalog_CategoryControllerTest extends Mage_Backend_Utility_Controller
{
    /**
     * Test index action
     */
    public function testIndexAction()
    {
        $this->dispatch('backend/admin/catalog_category/index');
        $body = $this->getResponse()->getBody();
        $this->assertNotContains('Sorry, you are using all the categories your account '
            . 'allows. To add more, first delete a category or upgrade your service.', $body);

        $pattern = '/<button[^>]*add_root_category_button[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnRoot = $matches[0];
        $this->assertNotContains('disabled="disabled"', $btnRoot,
            '"Add Root Category" button should be enabled on Categories page, if the limit is reached');

        $pattern = '/<button[^>]*add_subcategory_button[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnSub = $matches[0];
        $this->assertNotContains('disabled="disabled"', $btnSub,
            '"Add Subcategory" should be enabled on Categories page, if the limit is reached');

        $pattern = '/<button[^>]*Save\sCategory[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnSave = $matches[0];
        $this->assertNotContains('disabled=\"disabled\"', $btnSave,
            '"Save Category" button should be enabled on Categories page, if the limit is not reached');
    }

    /**
     * Test index action when maximum allowed number of categories is reached
     *
     * @magentoConfigFixture limitations/catalog_category 1
     */
    public function testIndexActionLimited()
    {
        $this->dispatch('backend/admin/catalog_category/index');
        $body = $this->getResponse()->getBody();

        $this->assertContains('Sorry, you are using all the categories your account allows.'
            . ' To add more, first delete a category or upgrade your service.', $body);

        $pattern = '/<button[^>]*add_root_category_button[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnRoot = $matches[0];
        $this->assertContains('disabled="disabled"', $btnRoot,
            '"Add Root Category" button should be disabled on Categories page, if the limit is reached');

        $pattern = '/<button[^>]*add_subcategory_button[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnSub = $matches[0];
        $this->assertContains('disabled="disabled"', $btnSub,
           '"Add Subcategory" should be disabled on Categories page, if the limit is reached');

        $pattern = '/<button[^>]*Save\sCategory[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnSave = $matches[0];

        $this->assertContains('disabled="disabled"', $btnSave,
            '"Save Category" button should be enabled on Categories page, if the limit is not reached');
    }

    /**
     * Test index action when limit is set but maximum allowed number of categories isn't reached
     *
     * @magentoConfigFixture limitations/catalog_category 2
     */
    public function testIndexActionLimitedAllowed()
    {
        $this->dispatch('backend/admin/catalog_category/index');
        $body = $this->getResponse()->getBody();
        $this->assertNotContains('Sorry, you are using all the categories your account allows.'
            . ' To add more, first delete a category or upgrade your service.', $body);

        $pattern = '/<button[^>]*add_root_category_button[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnRoot = $matches[0];
        $this->assertNotContains('disabled="disabled"', $btnRoot,
            '"Add Root Category" button should be enabled on Categories page, if the limit is not reached');

        $pattern = '/<button[^>]*add_subcategory_button[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnSub = $matches[0];
        $this->assertNotContains('disabled="disabled"', $btnSub,
            '"Add Subcategory" should be enabled on Categories page, if the limit is not reached');

        $pattern = '/<button[^>]*Save\sCategory[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnSave = $matches[0];
        $this->assertNotContains('disabled="disabled"', $btnSave,
            '"Save Category" button should be enabled on Categories page, if the limit is not reached');
    }

    /**
     * Test edit action
     */
    public function testEditAction()
    {
        $this->getRequest()->setParam('id', 2);
        $this->getRequest()->setQuery('isAjax', 'true');
        $this->dispatch('backend/admin/catalog_category/edit');
        $body = $this->getResponse()->getBody();

        $pattern = '/<button[^>]*Save\sCategory[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnSave = $matches[0];
        $this->assertNotContains('disabled=\"disabled\"', $btnSave,
            '"Save Category" button should be enabled on Categories page, if the limit is not reached');
    }

    /**
     * Test edit action when maximum allowed number of categories is reached for existing category
     *
     * @magentoConfigFixture limitations/catalog_category 1
     */
    public function testEditActionLimitedExistingCategory()
    {
        $this->getRequest()->setParam('id', 2);
        $this->getRequest()->setQuery('isAjax', 'true');
        $this->dispatch('backend/admin/catalog_category/edit');
        $body = $this->getResponse()->getBody();

        $pattern = '/<button[^>]*Save\sCategory[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnSave = $matches[0];
        $this->assertNotContains('disabled=\"disabled\"', $btnSave,
            '"Save Category" button should be enabled for saving existing category, even if the limit is reached');
    }

    /**
     * Test edit action when maximum allowed number of categories is reached for new category
     *
     * @magentoConfigFixture limitations/catalog_category 1
     */
    public function testEditActionLimitedNewCategory()
    {
        $this->getRequest()->setParam('id', null);
        $this->getRequest()->setQuery('isAjax', 'true');
        $this->dispatch('backend/admin/catalog_category/edit');
        $body = $this->getResponse()->getBody();

        $pattern = '/<button[^>]*Save\sCategory[^>]*>/';
        preg_match($pattern, $body, $matches);
        $this->assertNotEmpty($matches[0]);
        $btnSave = $matches[0];
        $this->assertContains('disabled=\"disabled\"', $btnSave,
            '"Save Category" button should be disabled for saving new category, if the limit is reached');
    }
}
