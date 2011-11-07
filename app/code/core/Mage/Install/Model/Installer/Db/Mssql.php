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
 * @category    Mage
 * @package     Mage_Install
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Mssql resource data model
 *
 * @category   Mage
 * @package    Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Model_Installer_Db_Mssql extends Mage_Install_Model_Installer_Db_Abstract
{
    /**
     * Retrieve DB server version
     *
     * @return string (string version number | 'undefined')
     */
    public function getVersion()
    {
        /*
         xp_msver, for any option, returns the four-column headings with values for that option
         Index | Name | Internal_Value | Character_Value
        */
        $stmt       = $this->_getConnection()->query('EXEC master..xp_msver ProductVersion');
        $version    = $stmt->fetchColumn(3);
        $version    = $version ? $version : 'undefined';
        return $version;
    }

    /**
     * Retrieve required PHP extension list for database
     *
     * @return array
     */
    public function getRequiredExtensions()
    {
        $extensions = parent::getRequiredExtensions();
        $extensions[] = (string)Mage::getConfig()
                ->getNode(sprintf('install/databases/mssql/pdo_types/%s', $this->getPdoType()));
        return $extensions;
    }

    /**
     * Return pdo type for current OS
     *
     * @return string
     */
    public function getPdoType()
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? 'sqlsrv' : 'dblib';
    }


}

