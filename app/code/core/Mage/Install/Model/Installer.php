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
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Installer model
 *
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Install_Model_Installer extends Varien_Object
{
    const XML_PATH_INSTALL_DATE     = 'global/install/date';
    const INSTALLER_HOST_RESPONSE   = 'MAGENTO';
    
    /**
     * Checking install status of application
     *
     * @return bool
     */
    public function isApplicationInstalled()
    {
        $installDate = Mage::getConfig()->getNode(self::XML_PATH_INSTALL_DATE);
        if ($installDate && strtotime($installDate)) {
            return true;
        }
        return false;
    }
    
    /**
     * Check server settings
     *
     * @return bool
     */
    public function checkServer()
    {
        try {
            Mage::getModel('install/installer_filesystem')->install();
            Mage::getModel('install/installer_env')->install();
            $result = true;
        } catch (Exception $e) {
            $result = false;
        }
        $this->setServerCheckStatus($result);
        return $result;
    }
    
    /**
     * Retrieve server checking result status
     *
     * @return unknown
     */
    public function getServerCheckStatus()
    {
        $status = $this->getData('server_check_status');
        if (is_null($status)) {
            $status = $this->checkServer();
        }
        return $status;
    }
    
    /**
     * Installation config data
     *
     * @param   array $data
     * @return  Mage_Install_Model_Installer
     */
    public function installConfig($data)
    {
        $data['db_active'] = true;
        Mage::getSingleton('install/installer_db')->checkDatabase($data);
        Mage::getSingleton('install/installer_config')
            ->setConfigData($data)
            ->install();
        return $this;
    }
    
    /**
     * Database installation
     *
     * @return Mage_Install_Model_Installer
     */
    public function installDb()
    {
        Mage_Core_Model_Resource_Setup::applyAllUpdates();
        return $this;
    }
    
    public function createAdministrator($data)
    {
        $user = Mage::getModel('admin/user')->load(1)->addData($data);
        $user->save();

        /*Mage::getModel("permissions/user")->setRoleId(1)
            ->setUserId($user->getId())
            ->setFirstname($user->getFirstname())
            ->add();*/

        return $this;
    }
    
    public function installEnryptionKey($key)
    {
        Mage::getSingleton('install/installer_config')->replaceTmpEncryptKey($key);
        return $this;
    }
    
    public function finish()
    {
        Mage::getSingleton('install/installer_config')->replaceTmpInstallDate();
        return $this;
    }
}