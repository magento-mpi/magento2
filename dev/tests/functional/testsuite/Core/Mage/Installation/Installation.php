<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Installation
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer registration tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Installation_Installation extends Mage_Selenium_TestCase
{
    /**
     * it's need to add some data before running test('database_name', 'user_name', 'user_password', 'base_url')
     * to 'install_magento' dataset
     */
    protected function assertPreConditions()
    {
        $data = $this->loadDataSet('Installation', 'install_magento/configuration');
        $host = $data['host'];
        $user = $data['user_name'];
        $password = $data['user_password'];
        $baseName = $data['database_name'];
        mysql_connect($host, $user, $password) or die("Couldn't connect to MySQL server!");
        mysql_query("DROP DATABASE IF EXISTS `$baseName`");
        mysql_query("CREATE DATABASE `$baseName`") or die("Couldn't create DATABASE!");
        //for local build
        //$this->installationHelper()->removeInstallData();
    }

    /**
     * @test
     */
    public function installTest()
    {
        $data = $this->loadDataSet('Installation', 'install_magento');
        $this->installationHelper()->installMagento($data);
    }
}