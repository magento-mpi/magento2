<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Core Observer model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Observer
{
    /**
     * Theme list
     *
     * @var array
     */
    protected $_themeList = array();

    /**
     * Check if synchronize process is finished and generate notification message
     *
     * @param  Varien_Event_Observer $observer
     * @return Mage_Core_Model_Observer
     */
    public function addSynchronizeNotification(Varien_Event_Observer $observer)
    {
        $adminSession = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        if (!$adminSession->hasSyncProcessStopWatch()) {
            $flag = Mage::getSingleton('Mage_Core_Model_File_Storage')->getSyncFlag();
            $state = $flag->getState();
            if ($state == Mage_Core_Model_File_Storage_Flag::STATE_RUNNING) {
                $syncProcessStopWatch = true;
            } else {
                $syncProcessStopWatch = false;
            }

            $adminSession->setSyncProcessStopWatch($syncProcessStopWatch);
        }
        $adminSession->setSyncProcessStopWatch(false);

        if (!$adminSession->getSyncProcessStopWatch()) {
            if (!isset($flag)) {
                $flag = Mage::getSingleton('Mage_Core_Model_File_Storage')->getSyncFlag();
            }

            $state = $flag->getState();
            if ($state == Mage_Core_Model_File_Storage_Flag::STATE_FINISHED) {
                $flagData = $flag->getFlagData();
                if (isset($flagData['has_errors']) && $flagData['has_errors']) {
                    $severity       = Mage_AdminNotification_Model_Inbox::SEVERITY_MAJOR;
                    $title          = Mage::helper('Mage_Adminhtml_Helper_Data')->__('An error has occured while syncronizing media storages.');
                    $description    = Mage::helper('Mage_Adminhtml_Helper_Data')->__('One or more media files failed to be synchronized during the media storages syncronization process. Refer to the log file for details.');
                } else {
                    $severity       = Mage_AdminNotification_Model_Inbox::SEVERITY_NOTICE;
                    $title          = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Media storages synchronization has completed!');
                    $description    = Mage::helper('Mage_Adminhtml_Helper_Data')->__('Synchronization of media storages has been successfully completed.');
                }

                $date = date('Y-m-d H:i:s');
                Mage::getModel('Mage_AdminNotification_Model_Inbox')->parse(array(
                    array(
                        'severity'      => $severity,
                        'date_added'    => $date,
                        'title'         => $title,
                        'description'   => $description,
                        'url'           => '',
                        'internal'      => true
                    )
                ));

                $flag->setState(Mage_Core_Model_File_Storage_Flag::STATE_NOTIFIED)->save();
            }

            $adminSession->setSyncProcessStopWatch(false);
        }

        return $this;
    }

    /**
     * Cron job method to clean old cache resources
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function cleanCache(Mage_Cron_Model_Schedule $schedule)
    {
        Mage::app()->getCache()->clean(Zend_Cache::CLEANING_MODE_OLD);
        Mage::dispatchEvent('core_clean_cache');
    }

    /**
     * Theme registration
     *
     * @return Mage_Core_Model_Observer
     */
    public function themeRegistration()
    {
        /** @var $themeCollection Mage_Core_Model_Theme_Collection */
        $themeCollection = Mage::getModel('Mage_Core_Model_Theme_Collection');
        try {
            $themeCollection->addDefaultPattern();
            foreach ($themeCollection as $theme) {
                $this->_saveThemeRecursively($theme, $themeCollection);
            }
        } catch (Mage_Core_Exception $e) {
            Mage::log($e->getMessage());
        }

        return $this;
    }

    /**
     * Save theme recursively
     *
     * @throws Mage_Core_Exception
     * @param string $theme
     * @param Mage_Core_Model_Theme_Collection $collection
     * @return Mage_Core_Model_Observer
     */
    protected function _saveThemeRecursively($theme, $collection)
    {
        $themeModel = $this->_loadThemeByPath($theme->getThemePath());
        if ($themeModel->getId()) {
            return $this;
        }

        $this->_addThemeToList($theme->getThemePath());
        if ($theme->getParentTheme()) {
            $parentTheme = $this->_prepareParentTheme($theme, $collection);
            if (!$parentTheme->getId()) {
                Mage::throwException('Invalid parent theme path');
            }
            $theme->setParentId($parentTheme->getId());
        }

        $theme->save();
        $this->_emptyThemeList();
        return $this;
    }

    /**
     * Prepare parent theme
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Theme_Collection $collection
     * @return Mage_Core_Model_Theme
     */
    protected function _prepareParentTheme($theme, $collection)
    {
        $parentThemePath = implode('/', $theme->getParentTheme());
        $themeModel = $this->_loadThemeByPath($parentThemePath);

        if (!$themeModel->getId()) {
            /**
             * Find theme model in file system collection
             */
            $filesystemThemeModel = $collection->getItemByColumnValue('theme_path', $parentThemePath);
            if ($filesystemThemeModel !== null) {
                $this->_saveThemeRecursively($filesystemThemeModel, $collection);
                return $filesystemThemeModel;
            }
        }

        return $themeModel;
    }

    /**
     * Add theme path to list
     *
     * @throws Mage_Core_Exception
     * @param string $themePath
     * @return Mage_Core_Model_Observer
     */
    protected function _addThemeToList($themePath)
    {
        if (in_array($themePath, $this->_themeList)) {
            Mage::throwException('Invalid parent theme(сross-references) leads to an infinite loop.');
        }
        array_push($this->_themeList, $themePath);
        return $this;
    }

    /**
     * Clear theme list
     *
     * @return Mage_Core_Model_Observer
     */
    protected function _emptyThemeList()
    {
        $this->_themeList = array();
        return $this;
    }

    /**
     * Load theme by path
     *
     * @param string  $themePath
     * @return Mage_Core_Model_Theme
     */
    protected function _loadThemeByPath($themePath)
    {
        /** @var $themeModel Mage_Core_Model_Theme */
        $themeModel = Mage::getModel('Mage_Core_Model_Theme');
        return $themeModel->load($themePath, 'theme_path');
    }
}
