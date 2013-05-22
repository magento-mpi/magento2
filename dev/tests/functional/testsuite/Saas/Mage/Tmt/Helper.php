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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Tmt_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Searches tenant by $searchData in TMT
     *
     * @param array $searchData
     * @param bool $flushRefresh
     * @return Tmt_Helper
     */
    public function searchForTenant($searchData, $flushRefresh = false) {
        $isTenantFound = true;
        $this->navigate('manage_tenants');
        $this->clickButton('reset');
        $this->fillForm($searchData);
        $this->clickButton('search', false);
        $this->waitForPageToLoad();
        $tenantNotFoundMessage = $this->controlIsPresent('message', 'error');
        if ($tenantNotFoundMessage) {
            $isTenantFound = false;
        }
        if ($isTenantFound == true) {
            $searchFieldset = $this->getUimapPage('tmt', 'manage_tenants')->findFieldset('search_tenant_results');
            $tenantIdField = $searchFieldset->findField('tenant_id');
            $this->waitForElement($tenantIdField);
            $tenantId = $this->initTenant();
            if ($flushRefresh == true) {
                $this->navigate('home');
                $this->clickControl('link', 'refresh_catalog', false);
                $this->clickControl('link', 'clear_css_js', false);
                $this->checkMessage('cleared_js_css');
                $this->navigate('home');
                $this->clickControl('link', 'flush_config', false);
                $this->checkMessage('flushed_config');
            }
            return $tenantId;
        }
        return $isTenantFound;
    }

    /*
     * Retrieve tenant id and edit tenent page id from manage tenants page
     */
    public function initTenant() {
        $searchFieldset = $this->getUimapPage('tmt', 'manage_tenants')->findFieldset('search_tenant_results');
        $xpath = $searchFieldset->findField('tenant_id');
        $tenantId = $this->getElement($xpath)->text();
        $this->addParameter('tenantId', $tenantId);
        $class = $this->getElement($this->_getControlXpath('field', 'tenant_row'))->attribute('class');
        $pageId = str_replace('grid-row-', '', $class);
        $this->addParameter('pageId', $pageId);
        return $tenantId;
    }

     /*
      * Retrieve database credentials from tenant local.xml
      */
    public function getTenantDbCredentials()
    {
        //get current page and area
        $page = $this->getCurrentPage();
        $area = $this->getArea();

        $config = $this->getApplicationConfig();
        preg_match('/(?<=[http|https]:\/\/)[a-zA-Z\-\.\d]+/', $config['areas']['frontend']['url'], $matches);
        $tenantDomain = $matches[0];
        $this->goToArea('tmt', 'home');
        if ($this->searchForTenant(array('domain' => $tenantDomain))){
            $this->navigate('edit_tenants');
            $localXml = $this->getElement($this->_getControlXpath('field', 'local_tab'))->value();
        } else {
            $this->fail('Tenant not found');
        }
        $keys = array('host', 'username', 'password', 'dbname');
        $config = new SimpleXMLElement($localXml);
        $connection = $config->xpath('//connection');
        foreach($keys as $key) {
            $data[$key] = (string)$connection[0]->$key;
        }

        //return to initial page
        if ($area == 'admin'){
            $this->loginAdminUser();
            $this->navigate($page);
        } else {
            $this->goToArea($area, $page);
        }

        return $data;
    }
}

