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
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Staging backup general info tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Backup_Edit_Tabs_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Keep main translate helper instance
     *
     * @var object
     */
    protected $helper;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->setFieldNameSuffix('staging_backup');
        $this->helper = Mage::helper('enterprise_staging');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('staging_backup_general_fieldset', array('legend'=>Mage::helper('enterprise_staging')->__('Backup Main Info')));

        $fieldset->addField('name', 'label', array(
            'label'     => $this->helper->__('Name'),
            'title'     => $this->helper->__('Name'),
            'value'      => $this->getBackup()->getStaging()->getName()
        ));
        
        $fieldset->addField('master_website', 'label', array(
            'label'     => $this->helper->__('Master Website'),
            'title'     => $this->helper->__('Master Website'),
            'value'      => $this->getMasterWebsite()->getName()
        ));
                
        $fieldset->addField('backupCreateAt', 'label', array(
            'label'     => $this->helper->__('Created Date'),
            'title'     => $this->helper->__('Created Date'),
            'value'     => $this->formatDate($this->getBackup()->getCreatedAt(), 'medium', true)
        ));
        
        $fieldset->addField('tablePrefix', 'label', array(
            'label'     => $this->helper->__('Table Prefix'),
            'title'     => $this->helper->__('Table Prefix'),
            'value'     => $this->getBackup()->getStagingTablePrefix()
        ));
        
        
/*        $fieldset = $form->addFieldset('staging_backup_used_tables', array('legend'=>Mage::helper('enterprise_staging')->__('Used Tables')));

        $usedTables = $this->_getBackupTables();
        if (is_array($usedTables)) {
            foreach($usedTables AS $tableName) {
                $fieldset->addField('UsedTable_' . $tableName, 'label', array(
                    'value'     => $tableName
                ));
            }
        }*/
        
        //$form->addValues($this->getBackup()->getData());        
        $form->setFieldNameSuffix($this->getFieldNameSuffix());

        $this->setForm($form);

        return parent::_prepareForm();
    }


    /**
     * get master website instance
     *
     * @return Mage_Core_Model_Website
     */
    public function getMasterWebsite()
    {

        $masterWebsiteId = $this->getBackup()->getStaging()->getMasterWebsiteIds();
        $masterWebsiteId = $masterWebsiteId[0];
        if (!is_null($masterWebsiteId)) {
            return Mage::app()->getWebsite($masterWebsiteId);
        } else {
            return false;
        }
    }
    
    
    /**
     * Retrieve currently edited backup object
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function getBackup()
    {
        if (!($this->getData('staging_backup') instanceof Enterprise_Staging_Model_Staging_Backup)) {
            $this->setData('staging_backup', Mage::registry('staging_backup'));
        }
        return $this->getData('staging_backup');
    }
    /**
     * Get backupped table list
     *
     * @return unknown
     */
    protected function _getBackupTables()
    {
        $backup = $this->getBackup();
        $stagingTablePrefix = $backup->getStagingTablePrefix();

        $connection = $backup->getStaging()->getAdapterInstance(true)
            ->getConnection("backup");
        
        // create sql
        $sql = "SHOW TABLES LIKE '{$stagingTablePrefix}%'";
        
        $result = $connection->fetchAll($sql);

        $resultArray = array();
        
        foreach ($result AS $row) {
            $table = array_values($row);
            $resultArray[] = $table[0];
        }
        return $resultArray;
    }
}
