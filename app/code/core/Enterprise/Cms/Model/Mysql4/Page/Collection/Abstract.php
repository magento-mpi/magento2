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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms page revision collection
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Model_Mysql4_Page_Collection_Abstract extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * Initialization
     *
     */
    protected function _construct()
    {
        $this->_map['fields']['user_id'] = 'main_table.user_id';
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

    /**
     * Add filtering by page id.
     * Parameter $page can be int or cms page object.
     *
     * @param mixed $page
     * @return Enterprise_Cms_Model_Mysql4_Collection_Abstract
     */
    public function addPageFilter($page)
    {
        if ($page instanceof Mage_Cms_Model_Page) {
            $page = $page->getId();
        }

        if (is_array($page)) {
            $page = array('in' => $page);
        }

        $this->addFieldTofilter('page_id', $page);

        return $this;
    }

    /**
     * Adds filter by version access level specified by owner.
     *
     * @param mixed $userId
     * @param mixed $accessLevel
     * @return Enterprise_Cms_Model_Mysql4_Collection_Abstract
     */
    public function addVisibilityFilter($userId, $accessLevel = Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC)
    {
        $_condition = array();

        if (is_array($userId)) {
            $_condition[] = $this->_getConditionSql(
                $this->_getMappedField('user_id'), array('in' => $userId));
        } else if ($userId){
            $_condition[] = $this->_getConditionSql(
                $this->_getMappedField('user_id'), $userId);
        }

        if (is_array($accessLevel)) {
            $_condition[] = $this->_getConditionSql(
                $this->_getMappedField('access_level'), array('in' => $accessLevel));
        } else {
            $_condition[] = $this->_getConditionSql(
                $this->_getMappedField('access_level'), $accessLevel);
        }

        $this->getSelect()->where(implode(' OR ', $_condition));

        return $this;
    }

    /**
     * Add filter by user.
     * Can take paramater user id or object.
     *
     * @param mixed $userId
     * @return Enterprise_Cms_Model_Mysql4_Collection_Abstract
     */
    public function addUserIdFilter($userId)
    {
        if ($userId instanceof Mage_Admin_Model_User) {
            $userId = $user->getId();
        }

        if (is_array($userId)) {
            $this->addFieldToFilter('page_id', array('in' => $userId));
        } else {
            $this->addFieldToFilter('page_id', $userId);
        }

        return $this;
    }
}
