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

class Enterprise_Staging_Model_Mysql4_Staging_Event_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('enterprise_staging/staging_event');
    }

    /**
     * Set staging filter into collection
     *
     * @param mixed $stagingId
     * @return Enterprise_Staging_Model_Mysql4_Staging_Event_Collection
     */
    public function setStagingFilter($stagingId)
    {
    	if (is_object($stagingId)) {
    		$stagingId = (int) $stagingId->getId();
    	}
    	if ($stagingId) {
    		$this->addFieldToFilter('staging_id', $stagingId);
    	}
    	return $this;
    }

    public function toOptionArray()
    {
        return parent::_toOptionArray('event_id', 'name');
    }

    public function toOptionHash()
    {
        return parent::_toOptionHash('event_id', 'name');
    }
}