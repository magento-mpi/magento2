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
 * Enterprise cms page config model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Cms_Model_Config
{
    const XML_PATH_CMS_TYPE_ATTRIBUTES = 'adminhtml/cms/revision_contol/';

    const XML_PATH_CMS_REVISION_CONTROL_STATUSES = 'adminhtml/cms/revision_contol/status';

    protected $_statuses;

    /**
     * Retrieves attributes for passed cms
     * type excluded from revision control.
     *
     * @return array
     */
    protected function _getRevisionControledAttributes($type) {
        $attributes = Mage::getConfig()
            ->getNode(self::XML_PATH_CMS_TYPE_ATTRIBUTES . $type)
            ->asArray();
        return array_keys($attributes);
    }

    /**
     * Retrieves cms page's attributes excluded from revision control.
     *
     * @return array
     */
    public function getPageRevisionControledAttributes()
    {
        return $this->_getRevisionControledAttributes('page');
    }

    /**
     * Returns array of access levels which can be viewed by current user.
     *
     * @return array
     */
    public function getAllowedAccessLevel()
    {
        if ($this->isCurrentUserCanPublishRevision()) {
            return array(
                Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PROTECTED,
                Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC
                );
        } else {
            return array(Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC);
        }
    }

    /**
     * Returns status of current user publish permission.
     *
     * @return bool
     */
    public function isCurrentUserCanPublishRevision()
    {
        return $this->_isAllowedAction('publish_revision');
    }

    /**
     * Return status of current user delete page permission.
     *
     * @return bool
     */
    public function isCurrentUserCanDeletePage()
    {
        return $this->_isAllowedAction('delete');
    }

    /**
     * Return status of current user create new page permission.
     *
     * @return bool
     */
    public function isCurrentUserCanSavePage()
    {
        return $this->_isAllowedAction('save');
    }

    /**
     * Return status of current user permission to save revision.
     *
     * @return bool
     */
    public function isCurrentUserCanSaveRevision()
    {
        return $this->_isAllowedAction('save_revision');
    }

    /**
     * Return status of current user permission to delete revision.
     *
     * @return bool
     */
    public function isCurrentUserCanDeleteRevision()
    {
        return $this->_isAllowedAction('delete_revision');
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function _isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }

    /**
     * Retrieve statuses from config
     *
     * @return array
     */
    public function getStatuses()
    {
        if (is_null($this->_statuses)) {
            $statusNode = Mage::getConfig()
                ->getNode(self::XML_PATH_CMS_REVISION_CONTROL_STATUSES);
            $this->_statuses = array();

            if ($statusNode) {
                foreach ($statusNode->children() as $key => $status) {
                    $this->_statuses[$key] = (string)$status->label;
                }
            }
        }

        return $this->_statuses;
    }
}
