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
class Enterprise_Mage_Rollback_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * <p>Rollback backup</p>
     *
     * @param string|array $rollbackData
     * @param string $filename
     */
    public function rollbackBackup($rollbackData, $filename = 'Backups')
    {
        if (is_string($rollbackData)) {
            $rollbackData = $this->loadDataSet($filename, $rollbackData);
        }
        $rollbackData = $this->clearDataArray($rollbackData);
        $searchWebsite = (isset($rollbackData['search_website'])) ? $rollbackData['search_website'] : array();
        $itemsToRollback = (isset($rollbackData['items_to_rollback'])) ? $rollbackData['items_to_rollback'] : array();

        if ($searchWebsite) {
            $this->openBackup($searchWebsite);
        }
        if ($itemsToRollback) {
            $this->clickControl('tab', 'rollback', false);
            $this->fillTab($itemsToRollback, 'rollback');
        }
        $this->saveForm('rollback');
    }

    /**
     * <p>Delete backup</p>
     *
     * @param string|array $searchWebsite
     * @param string $filename
     */
    public function deleteBackup($searchWebsite, $filename = 'Backups')
    {
        if (is_string($searchWebsite)) {
            $searchWebsite = $this->loadDataSet($filename, $searchWebsite);
        }
        $searchWebsite = $this->clearDataArray($searchWebsite);
        $this->openBackup($searchWebsite);
        $this->clickButtonAndConfirm('delete', 'confirmation_to_delete');
    }

    /**
     * Open backup
     *
     * @param string|array $searchWebsite
     * @param string $filename
     */
    public function openBackup($searchWebsite, $filename = 'Backups')
    {
        if (is_string($searchWebsite)) {
            $searchWebsite = $this->loadDataSet($filename, $searchWebsite);
        }
        $searchWebsite = $this->clearDataArray($searchWebsite);
        $this->addParameter('elementTitle', $searchWebsite['filter_website_name']);
        $param = "contains(.,'" . $searchWebsite['filter_website_name'] .  "')";
        $this->addParameter('param', $param);
        $this->addParameter('websiteName', $searchWebsite['filter_website_name']);
        $xpathTR = $this->_getControlXpath('pageelement', 'grid_tr');
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->fillDropdown('action', 'Edit');
        $this->waitForPageToLoad();
        $this->validatePage();
    }
}
