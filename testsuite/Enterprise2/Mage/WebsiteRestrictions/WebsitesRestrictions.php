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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * 
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise2_Mage_WebsiteRestrictions extends Mage_Selenium_TestCase
{
    /**
     * <p>Check Configuration Fields</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Check that all dropdowns are present:Access Restriction, Restriction Mode, Startup Page, Landing Page, HTTP Response </p>
     * <p>Expected result</p>
     * <p>All fields are present</p>    
     * 
     * @test
     */
     
    public function navigationTest()
    {
        //test comit push
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->openTab('general_general');
        $this->assertTrue($this->controlIsPresent('dropdown', 'access_restriction'), 'There is no "access_restriction" dropdown on the page');
        $this->assertTrue($this->controlIsPresent('dropdown', 'restriction_mode'), 'There is no "restriction_mode" dropdown on the page');  
        $this->assertTrue($this->controlIsPresent('dropdown', 'startup_page'), 'There is no "startup_page" dropdown on the page');  
        $this->assertTrue($this->controlIsPresent('dropdown', 'landing_page'), 'There is no "landing_page" dropdown on the page');  
        $this->assertTrue($this->controlIsPresent('dropdown', 'http_response'), 'There is no "http_response" dropdown on the page');
    }
    
    /**
     *
     * <p>Website Closed HTTP Response 200 OK</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Website Closed" in "Restriction Mode"</p>
     * <p>5.Select "503 Service Unavailable" in "Landing Page"</p>
     * <p>6.Select "200 OK" in "HTTP Response"</p>
     * <p>7.Open Frontend Home Page</p>
     * <p>Expected result</p>
     * <p>"503 Service Unavailable" page is open</p>    
     * <p>HTTP Responce is "200 OK"</p>
     *
     * @test
     */
     
    public function websiteClosedHttpResponse200()
    {
        //Data
        $config=$this->loadDataSet('WebsiteRestrictions', 'website_closed_response_200');
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('home_page', false);
        $this->websiteRestrictionsHelper()->validateFrontendHtttpCode('home_page', '200');           
        $pagetitle=$this->getTitle();
        $this->assertTrue($pagetitle=='503 Service Unavailable', "Wrong page title '$pagetitle'");        
    }
    
    /**
     *
     * <p>Website Closed HTTP Response 503</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Website Closed" in "Restriction Mode"</p>
     * <p>5.Select "503 Service Unavailable" in "Landing Page"</p>
     * <p>6.Select "503 Service Unavailable in "HTTP Response"</p>
     * <p>7.Open Frontend Home Page</p>
     * <p>Expected result</p>
     * <p>"503 Service Unavailable" page is open</p>    
     * <p>HTTP Responce is "503 Service Unavailable"</p> 
     *
     * @test
     */
     
    public function websiteClosedHttpResponse503()
    {
        //Data
        $config=$this->loadDataSet('WebsiteRestrictions', 'website_closed_response_503');
        //Preconditions
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('home_page', false);
        $this->websiteRestrictionsHelper()->validateFrontendHtttpCode('home_page', '503');           
        $pagetitle=$this->getTitle();
        $this->assertTrue($pagetitle=='503 Service Unavailable', "Wrong page title '$pagetitle'");        
    }
    /**
     * <p>Checkout in Restricted Mode</p>
     * <p>Preconditions:</p>
     * <p>1.Product is created</p>
     * <p>2.Customer is created</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Private Sales: Login Only" in "Restriction Mode"</p>
     * <p>5.Select "To login form" in "Startup Page"</p>
     * <p>6.Save config</p>
     * <p>7.Login to Frontend</p>
     * <p>8.Add product to Shopping Cart</p>
     * <p>9.Place order</p>
     * <p>Expected result</p>
     * <p>Order is successfully created</p>    
     * 
     * 
     * @test
     */
    public function checkoutInRestrictedMode()
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $config=$this->loadDataSet('WebsiteRestrictions', 'login_only_tologinform');
        $user=array('email'    => $userData['email'],
                    'password' => $userData['password']);
        $checkoutData = $this->loadDataSet('OnePageCheckout', 'exist_flatrate_checkmoney',
                                           array('general_name'  => $simple['general_name'],
                                                'email_address'  => $user['email']));
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);              
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData);
        //Steps
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->fillForm($user);
        $this->clickButton('login');
        $this->checkoutOnePageHelper()->frontCreateCheckout($checkoutData);
        //Verification
        $this->assertMessagePresent('success', 'success_checkout');
       
    }
    /**
     * <p>Register customer in Login and Register mode </p>
     * <p>Preconditions:</p>
     * <p>1.Product is created</p>
     * <p>2.Customer is created</p>
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Private Sales: Login and Register" in "Restriction Mode"</p>
     * <p>5.Select "To login form" in "Startup Page"</p>
     * <p>6.Save config</p>
     * <p>7.Open Frontend</p>
     * <p>8.Click "Register" button</p>
     * <p>9.Fill all required field</p>
     * <p>9.Click "Submit" button</p>
     * <p>Expected result</p>
     * <p>Customer is registered</p>
     * <p>Open customer Dashboard page</p>
     * <p>Show message "Thank you for registering with store name"</p> 
     * 
     * @test
     */
    public function registerCustomerInLoginAndRegisterMode()
    {
        //Data
        $user=$this->loadDataSet('Customers', 'customer_account_register');
        $config=$this->loadDataSet('WebsiteRestrictions', 'login_and_register_tologinform');
        //
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->customerHelper()->registerCustomer($user);
        //Verifying
        $this->assertMessagePresent('success', 'success_registration');
        $this->validatePage('customer_account');
        $this->logoutCustomer();
        
    }
    
    /**
     * <p>Register customer in Login Only mode </p>  
     * <p>Steps:</p>
     * <p>1.Login to Backend</p>
     * <p>2.Go to System->Configuration->Advanced->Developer->website Restrictions</p>
     * <p>3.Select "Yes" in "Acces Restriction"</p>
     * <p>4.Select "Private Sales: Login Only" in "Restriction Mode"</p>
     * <p>5.Select "To login form" in "Startup Page"</p>
     * <p>6.Save config</p>
     * <p>7.Open Frontend</p>
     * <p>8.Click "Register" link in header</p>   
     * <p>Expected result</p>
     * <p>Open Login Page </p>
     * <p>Open customer Dashboard page</p>
     * <p>Show message "Thank you for registering with store name"</p>  
     *
     * @test
     */
    public function registerCustomerInLoginOnlyMode()
    {
       
        //Data
        $config=$this->loadDataSet('WebsiteRestrictions', 'login_only_tologinform');
        //Precondition
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($config);
        $this->clearInvalidedCache();
        //Steps
        $this->frontend('home_page', false);
        $this->validatePage('customer_login');
        $this->frontend('home_page', false);
        $this->clickControl('link', 'register', false);
        //Verifying
        $this->validatePage('customer_login');
    }
    
}   
?>

