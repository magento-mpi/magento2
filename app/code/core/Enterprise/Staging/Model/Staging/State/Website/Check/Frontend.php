<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Staging_Model_Staging_State_Website_Check_Frontend extends Enterprise_Staging_Model_Staging_State_Website_Abstract
{
    /**
     * Main run method of current state
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @return  Enterprise_Staging_Model_Staging_State_Website_Merge
     */
    protected function _run(Enterprise_Staging_Model_Staging $staging)
    {
        if (Mage::registry('staging/frontend_checked_started')) {
            return $this;
        }
        Mage::register('staging/frontend_checked_started', true);

        $stagingItems   = Enterprise_Staging_Model_Staging_Config::getStagingItems();
        foreach ($stagingItems->children() as $stagingItem) {
            if (!$stagingItem->is_backend) {
                continue;
            }

            $adapter = $this->getItemAdapterInstanse($stagingItem);
            $adapter->checkFrontend($staging);

            if (!empty($stagingItem->extends) && is_object($stagingItem->extends)) {
                foreach ($stagingItem->extends->children() AS $extendItem) {
                    if (!$extendItem->is_backend) {
                        continue;
                    }
                    $adapter = $this->getItemAdapterInstanse($extendItem);
                    $adapter->checkFrontend($staging);
                }
            }
        }
        Mage::register('staging/frontend_checked', true);
        Mage::unregister('staging/frontend_checked_started');
        return $this;
    }
}