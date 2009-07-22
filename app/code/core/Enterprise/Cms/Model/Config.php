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

    /**
     * WYSIWYG flags
     */
    const WYSIWYG_ENABLED_DEFAULT = 1;
    const WYSIWYG_DISABLED_DEFAULT = 2;
    const WYSIWYG_DISABLED_TOTALLY = 3;

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
        return array_keys($attributes);;
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
        if ($this->isCurrentUserCanPublish()) {
            return array(
                Enterprise_Cms_Model_Version::ACCESS_LEVEL_PROTECTED,
                Enterprise_Cms_Model_Version::ACCESS_LEVEL_PUBLIC
                );
        } else {
            return array(Enterprise_Cms_Model_Version::ACCESS_LEVEL_PUBLIC);
        }
    }

    /**
     * Returns status of current user publish permission.
     *
     * @return bool
     */
    public function isCurrentUserCanPublish()
    {
        return $this->isAllowedAction('publish_revision');
    }

    /**
     * Return status of current user delete page permission.
     *
     * @return bool
     */
    public function isCurrentUserCanDeletePage()
    {
        return $this->isAllowedAction('delete');
    }

    /**
     * Return status of current user create new page permission.
     *
     * @return bool
     */
    public function isCurrentUserCanCreatePage()
    {
        return $this->isAllowedAction('new');
    }

    /**
     * Return status of current user permission to save revision.
     *
     * @return bool
     */
    public function isCurrentUserCanSave()
    {
        return $this->isAllowedAction('save');
    }

    /**
     * Check permission for passed action
     *
     * @param string $action
     * @return bool
     */
    protected function isAllowedAction($action)
    {
        return Mage::getSingleton('admin/session')->isAllowed('cms/page/' . $action);
    }
}
