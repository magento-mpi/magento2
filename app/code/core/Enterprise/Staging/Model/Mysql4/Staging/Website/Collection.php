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

class Enterprise_Staging_Model_Mysql4_Staging_Website_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('enterprise_staging/staging_website');
    }

    /**
     * Set staging filter into collection select
     *
     * @param   mixed   $entitySetId (if object must be implemented getId() method)
     * @return  object  Enterprise_Staging_Model_Mysql4_Staging_Website_Collection
     */
    public function addStagingFilter($stagingId)
    {
    	if (is_object($stagingId)) {
    		$stagingId = $stagingId->getId();
    	}
        $this->addFieldToFilter('staging_id', (int) $stagingId);

        return $this;
    }

    /**
     * Retrieve item from collection where "code" attribute value equals to given code
     *
     * @param   string $code
     * @return  object Enterprise_Staging_Model_Staging_Website
     */
    public function getItemByCode($code)
    {
    	foreach ($this->_items as $item) {
    		if ($item->getCode() == (string) $code) {
    			return $item;
    		}
    	}
    	return false;
    }

    /**
     * Retrieve item from collection where "master_website_code" attribute value equals to given code
     *
     * @param   string $code
     * @return  object Enterprise_Staging_Model_Staging_Website
     */
    public function getItemByMasterCode($code)
    {
    	foreach ($this->_items as $item) {
            if ($item->getMasterWebsiteCode() == (string) $code) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Retrieve item from collection where "master_website_id" attribute value equals to given id
     *
     * @param   int     $id
     * @return  object  Enterprise_Staging_Model_Staging_Website
     */
    public function getItemByMasterId($id)
    {
        foreach ($this->_items as $item) {
            if ($item->getMasterWebsiteId() == (string) $id) {
                return $item;
            }
        }
        return false;
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('staging_website_id', 'name');
    }

    public function toOptionHash()
    {
        return parent::_toOptionHash('staging_website_id', 'name');
    }
}