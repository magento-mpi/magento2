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
 * @package    Enterprise_CustomerBalance
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_CustomerBalance_Model_Mysql4_Balance_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	protected $_map = array('fields' => array('website_id' => 'main_table.website_id', 'core_website_id' => 'w.website_id'));
	
    protected function _construct()
    {
        $this->_init('enterprise_customerbalance/balance');
    }

    public function addWebsiteData()
    {
        $this->getSelect()->joinInner(array('w' => $this->getTable('core/website')), 'main_table.website_id = w.website_id');
        return $this;
    }

    /**
     * Enter description here...
     *
     * @param int|array $website
     */
    public function addWebsiteFilter($website)
    {
        $this->getSelect()->where(
            $this->getConnection()->quoteInto('main_table.website_id IN(?)', $website)
        );
        return $this;
    }
}