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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_Staging_Model_Staging_State_Website_Merge extends Enterprise_Staging_Model_Staging_State_Website_Abstract
{
    /**
     * Boolean flag that confirm to create event history record
     * after current state is done
     *
     * @var boolean
     */
    protected $_addToEventHistory = true;

    /**
     * Main run method of current state
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_State_Website_Merge
     */
    protected function _run(Enterprise_Staging_Model_Staging $staging)
    {
        $this->_mergeStagingWebsites($staging);
        return $this;
    }

    /**
     * Merge selected items data from staging websites to mapped ones
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_State_Website_Merge
     */
    protected function _mergeStagingWebsites($staging)
    {
        foreach ($staging->getWebsitesCollection() as $stagingWebsite) {
            $this->_mergeStagingWebsite($staging, $stagingWebsite);
        }
        return $this;
    }

    /**
     * Merge selected items data from staging website to mapped one
     *
     * @param   Enterprise_Staging_Model_Staging            $staging
     * @param   Enterprise_Staging_Model_Staging_Website    $stagingWebsite
     * @return  Enterprise_Staging_Model_Staging_State_Website_Merge
     */
    protected function _mergeStagingWebsite($staging, $stagingWebsite)
    {
        $usedItems = $staging->getMapperInstance()->getUsedItems();
        foreach ($usedItems as $usedItem) {
            $itemXmlConfig = Enterprise_Staging_Model_Staging_Config::getStagingItem($usedItem['code']);
            if ($itemXmlConfig) {
                $adapter = $this->getItemAdapterInstanse($itemXmlConfig);
                $adapter->mergeItem($staging, $stagingWebsite, $itemXmlConfig);
            }
        }
        return $this;
    }
}