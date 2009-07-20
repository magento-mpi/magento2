<?php

class Enterprise_Cms_Model_Config
{
    const XML_PATH_CMS_TYPE_ATTRIBUTES = 'adminhtml/cms/revision_contol/';

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
