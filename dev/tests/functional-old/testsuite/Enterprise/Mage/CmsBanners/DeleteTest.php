<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_CmsBanners
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Delete Banner Test
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_CmsBanners_DeleteTest extends Mage_Selenium_TestCase
{
    protected function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Creates and deletes Banner with required fields</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6022
     */
    public function deleteCmsBanner()
    {
        //Data
        $pageData = $this->loadDataSet('CmsBanners', 'new_cms_banner_req');
        $search = array('filter_banner_name' => $pageData['banner_properties']['banner_properties_name']);
        //Steps
        $this->navigate('manage_cms_banners');
        $this->cmsBannersHelper()->createCmsBanner($pageData);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_cms_banner');
        //Steps
        $this->cmsBannersHelper()->deleteCmsBanner($search);
        //Verification
        $this->assertMessagePresent('success', 'success_deleted_cms_banner');
    }

    /**
     * <p>Delete several banners.</p>
     *
     * @test
     * @depends deleteCmsBanner
     * @TestlinkId TL-MAGE-6023
     */
    public function throughMassAction()
    {
        $bannerQty = 2;
        for ($i = 1; $i <= $bannerQty; $i++) {
            //Data
            $bannerData = $this->loadDataSet('CmsBanners', 'new_cms_banner_req');
            ${'searchData' . $i} =
                $this->loadDataSet('CmsBanners', 'search_cms_banner_page', array('filter_banner_name' =>
                    $bannerData['banner_properties']['banner_properties_name']));
            //Steps
            $this->navigate('manage_cms_banners');
            $this->cmsBannersHelper()->createCmsBanner($bannerData);
            //Verifying
            $this->assertMessagePresent('success', 'success_saved_cms_banner');
        }
        for ($i = 1; $i <= $bannerQty; $i++) {
            $this->searchAndChoose(${'searchData' . $i}, 'cms_banners_grid');
        }
        $this->addParameter('qtyDeletedProducts', $bannerQty);
        $this->fillDropdown('banner_massaction', 'Delete');
        $this->clickButtonAndConfirm('submit', 'confirmation_for_mass_delete');
        //Verifying
        $this->assertMessagePresent('success', 'success_deleted_banner_massaction');
    }
}