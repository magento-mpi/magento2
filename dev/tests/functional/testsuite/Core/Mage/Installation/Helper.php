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

class Core_Mage_Installation_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Delete installation files
     *
     * @return null
     */
    public function removeInstallData()
    {
        $basePath = $this->getConfigHelper()->getBaseUrl();
        $localXml = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'etc'
                    . DIRECTORY_SEPARATOR . 'local.xml';
        $cacheDir = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'cache';
        if (file_exists($localXml)) {
            unlink($localXml);
        }
        $this->_rmRecursive($cacheDir);
    }

    /**
     * Remove fs element with nested elements
     *
     * @param string $dir
     *
     * @return null
     */
    protected function _rmRecursive($dir)
    {
        if (is_dir($dir)) {
            foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $object) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $object)) {
                    $this->_rmRecursive($dir . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
            reset($object);
            rmdir($dir);
        } else {
            unlink($dir);
        }
    }

    /**
     * @param array $installData
     */
    public function installMagento(array $installData)
    {
        $licenseAgreement = (isset($installData['license_agreement'])) ? $installData['license_agreement'] : array();
        $localization = (isset($installData['localization'])) ? $installData['localization'] : array();
        $configuration = (isset($installData['configuration'])) ? $installData['configuration'] : array();
        $adminAccount = (isset($installData['admin_account'])) ? $installData['admin_account'] : array();
        $this->frontend('home_page', false);
        //'License Agreement' page
        $this->validatePage('license_agreement');
        $this->fillFieldset($licenseAgreement, 'license_agreement');
        $this->clickButton('continue', false);
        $this->waitForNewPage();
        $this->assertMessageNotPresent('error');
        //'Localization' page
        $this->validatePage('localization');
        $this->fillFieldset($localization, 'local_settings');
        //Add 'config' parameter to UIMap
        $fields = array('locale', 'timezone', 'currency');
        $config = '?';
        foreach ($fields as $number => $field) {
            $selected = $this->getControlAttribute('dropdown', $field, 'selectedValue');
            $config .= 'config[' . $field . ']=' . $selected;
            if (array_key_exists($number + 1, $fields)) {
                $config .= '&';
            }
        }
        $this->addParameter('config', urlencode($config));
        $this->clickButton('continue', false);
        $this->waitForNewPage();
        $this->assertMessageNotPresent('error');
        // 'Configuration' page
        $this->validatePage('configuration');
        $dataBase = (isset($configuration['database_type'])) ? $configuration['database_type'] : '';
        $this->fillDropdown('database_type', $dataBase);
        $this->addParameter('dbType',
            strtolower($this->getControlAttribute('dropdown', 'database_type', 'selectedValue')));
        $this->fillForm($configuration);
        $this->clickButton('continue', false);
        $this->assertMessageNotPresent('validation');
        $this->waitForNewPage();
        $this->assertMessageNotPresent('error');
        // 'Create Admin Account' page
        $this->validatePage('create_admin_account');
        $this->fillForm($adminAccount);
        $this->clickButton('continue', false);
        $this->assertMessageNotPresent('validation');
        $this->waitForNewPage();
        $this->assertMessageNotPresent('error');
        // 'You're All Set!' page
        $this->validatePage('end_installation');
        //@TODO set new url
        // Log in to Admin
        $this->loginAdminUser();
        //Go to Frontend
        $this->frontend();
    }
}
