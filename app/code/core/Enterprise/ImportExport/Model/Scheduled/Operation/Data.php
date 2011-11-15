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
 * @package     Enterprise_ImportExport
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Operation Data model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_ImportExport_Model_Scheduled_Operation_Data
{
    const STATUS_PENDING = 2;

    /**
     * Get statuses option array
     *
     * @return array
     */
    public function getStatusesOptionArray()
    {
        return array(
            1 => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Enabled'),
            0 => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Disabled'),
        );
    }

    /**
     * Get operations option array
     *
     * @return array
     */
    public function getOperationsOptionArray()
    {
        return array(
            'import' => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Import'),
            'export' => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Export')
        );
    }

    /**
     * Get frequencies option array
     *
     * @return array
     */
    public function getFrequencyOptionArray()
    {
        return array(
            Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAILY   => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Daily'),
            Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY  => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Weekly'),
            Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Monthly'),
        );
    }

    /**
     * Get server types option array
     *
     * @return array
     */
    public function getServerTypesOptionArray()
    {
        return array(
            'file'  => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Local Server'),
            'ftp'   => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Remote FTP')
        );
    }

    /**
     * Get file modes option array
     *
     * @return array
     */
    public function getFileModesOptionArray()
    {
        return array(
            FTP_BINARY  => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Binary'),
            FTP_ASCII   => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('ASCII'),
        );
    }

    /**
     * Get forced import option array
     *
     * @return array
     */
    public function getForcedImportOptionArray()
    {
        return array(
            0 => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Stop Import'),
            1 => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Continue Processing'),
        );
    }

    /**
     * Get operation result option array
     *
     * @return array
     */
    public function getResultOptionArray()
    {
        return array(
            0  => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Failed'),
            1  => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Successful'),
            self::STATUS_PENDING  => Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Pending')
        );
    }

    /**
     * Get entities option array
     *
     * @param string $type
     * @return array
     */
    public function getEntitiesOptionArray($type = null)
    {
        $entitiesPath = Mage_ImportExport_Model_Import::CONFIG_KEY_ENTITIES;
        $importEntities = Mage_ImportExport_Model_Config::getModelsArrayOptions($entitiesPath);

        $entitiesPath = Mage_ImportExport_Model_Export::CONFIG_KEY_ENTITIES;
        $entities = Mage_ImportExport_Model_Config::getModelsArrayOptions($entitiesPath);

        switch ($type) {
            case 'import':
                return $importEntities;
            case 'export':
                return $entities;
            default:
                foreach ($importEntities as $key => &$entityName) {
                    $entities[$key] = $entityName;
                }
                return $entities;
        }
    }
}
