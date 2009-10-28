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
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms Hierarchy Pages Lock Model
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Hierarchy_Lock extends Varien_Object
{
    /**
     * Lock file
     *
     * @var null|Varien_Io_File
     */
    protected $_lockFile = null;

    /**
     * Admin session model
     *
     * @var Mage_Admin_Model_Session
     */
    protected $_adminSession;

    /**
     * Lock data flag
     *
     * @var bool
     */
    protected $_lockDataFlag = false;
    /**
     * Set admin session model
     *
     * @param Mage_Admin_Model_Session $adminSessionModel
     */
    public function __construct(Mage_Admin_Model_Session $adminSessionModel)
    {
        $this->_adminSession = $adminSessionModel;
    }

    /**
     * Check whether page is locked for current user
     *
     * @return bool
     */
    public function isLocked()
    {
        return $this->_getLockFile()->fileExists($this->_getLockFileName()) && $this->isActual();
    }

    /**
     * Check whether lock belongs to current user
     *
     * @return bool
     */
    public function isLockedByMe()
    {
        return ($this->isLocked() && $this->isLockOwner());
    }

    /**
     * Check whether lock belongs to other user
     *
     * @return bool
     */
    public function isLockedByOther()
    {
        return ($this->isLocked() && !$this->isLockOwner());
    }

    /**
     * Revalidate lock data
     *
     * @return Enterprise_Cms_Model_Hierarchy_Lock
     */
    public function revalidate()
    {
        if (!$this->isEnabled()) {
            return $this;
        }
        if (!$this->isLocked() || $this->isLockedByMe()) {
            $this->lock();
        }
        $this->loadLockData();
        return $this;
    }

    /**
     * Load lock data in model
     *
     * @return Enterprise_Cms_Model_Hierarchy_Lock
     */
    public function loadLockData()
    {
        if (!$this->_lockDataFlag) {
            $data = unserialize($this->_getLockFile()->read($this->_getLockFileName()));
            foreach ($data as $key => $value) {
                $this->setData($key, $value);
            }
            $this->_lockDataFlag = true;
        }
        return $this;
    }

    /**
     * Check whether lock is actual
     *
     * @return bool
     */
    public function isActual()
    {
        return (filemtime($this->_getLockFilesPath() . $this->_getLockFileName()) + $this->getLockLifeTime() > time());
    }

    /**
     * Check whether lock functionality is enabled
     *
     * @return bool
     */
    public function isEnabled()
    {
        return ($this->getLockLifeTime() > 0);
    }

    /**
     * Check whether current user is lock owner
     *
     * @return bool
     */
    public function isLockOwner()
    {
        $this->loadLockData();
        return ($this->getUserName() == $this->_getSession()->getUser()->getName());
    }

    /**
     * Create lock for page
     *
     * @return Enterprise_Cms_Model_Hierarchy_Lock
     */
    public function lock()
    {
        $file = $this->_getLockFile();
        $file->streamOpen($this->_getLockFileName());
        $lockData = array(
            'session_id'    => $this->_getSession()->getSessionId(),
            'user_name'     => $this->_getSession()->getUser()->getName()
        );
        $file->streamWrite(serialize($lockData));
        return $this;
    }

    /**
     * Retrieve lock lifetime from config
     *
     * @return int
     */
    public function getLockLifeTime()
    {
        return (int)Mage::getStoreConfig('cms/hierarchy/lock_timeout');
    }

    /**
     * Retrieve lock file path
     *
     * @return string
     */
    protected function _getLockFilesPath()
    {
        return Mage::getBaseDir('var') . DS . 'locks' . DS;
    }

    /**
     * Retrieve lock file name
     *
     * @return string
     */
    protected function _getLockFileName()
    {
        return 'cms_hierarchy.lock';
    }

    /**
     * Retrieve lock file
     *
     * @return Varien_Io_File
     */
    protected function _getLockFile()
    {
        if (is_null($this->_lockFile)) {
            $file = new Varien_Io_File;
            $file->setAllowCreateFolders(true)->open(array(
                'path' => $this->_getLockFilesPath()
            ));
            $this->_lockFile = $file;
        }
        return $this->_lockFile;
    }

    /**
     * Retrieve admin session model
     *
     * @return Mage_Admin_Model_Session
     */
    protected function _getSession()
    {
        return $this->_adminSession;
    }
}