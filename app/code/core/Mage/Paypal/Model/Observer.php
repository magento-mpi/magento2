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
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * PayPal module observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Paypal_Model_Observer
{
    const REPORTS_HOSTNAME = "reports.paypal.com";
    const SANDBOX_REPORTS_HOSTNAME = "reports.sandbox.paypal.com";
    const REPORTS_PATH = "/ppreports/outgoing";

    /**
     *
     * Iterate through website configurations and get all configurations
     * for SFTP from there. No filtering is done here.
     *
     */
    protected function _getSftpConfigs()
    {
        $stores = Mage::app()->getStores();
        $configs = array();
        foreach($stores as $store) {
            $configs[] = array(
                'hostname' => $store->getConfig('paypal/fetch_reports/ftp_ip'),
                'path' => $store->getConfig('paypal/fetch_reports/ftp_path'),
                'username' => $store->getConfig('paypal/fetch_reports/ftp_login'),
                'password' => $store->getConfig('paypal/fetch_reports/ftp_password'),
                'enabled' =>  $store->getConfig('paypal/fetch_reports/active'),
                'sandbox' =>  $store->getConfig('paypal/fetch_reports/ftp_sandbox'),
            );
        }
        return $configs;
    }

    /**
     * Takes in a list of report configurations and returns only enabled
     * ones.
     *
     * @param $configs Array of configurations as produced by _getSftpConfigs()
     */
    protected function _filterSftpConfigs($configs)
    {
        $result = array();
        foreach($configs as $config){
            if ($config['enabled']){
                if (empty($config['hostname'])) {
                    $config['hostname'] = $config['sandbox'] ? self::SANDBOX_REPORTS_HOSTNAME : self::REPORTS_HOSTNAME;
                }
                if (empty($config['path'])) {
                    $config['path'] = self::REPORTS_PATH;
                }
                $result[] = $config;
            }
        }
        return $result;
    }

    /**
     * Goes to reports.paypal.com and fetches the reports.
     */
    public function fetchReports()
    {
        try {
            Mage::log("Fetch reports started.");
            $configs = $this->_filterSftpConfigs($this->_getSftpConfigs());
            if (!count($configs)) {
                Mage::log('Nothing to do: no usable configs found.');
            } else {
                foreach ($configs as $config) {
                    Mage::getModel("paypal/report_settlement")->fetchAndSave($config['hostname'], $config['username'], $config['password'], $config['path']);
                }
            }
        }
        catch (Mage_Core_Exception $e) {
            Mage::logException($e);
        }
    }
}
