<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Staging Rollback Grid
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Rollback_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);


        $this->setTemplate('enterprise/staging/manage/staging/rollback/grid.phtml');
    }

    /**
     * PrepareCollection method.
     */
    protected function _prepareCollection()
    {
        $staging       = $this->getStaging();
        $itemCollection    = $staging->getDatasetItemsCollection(true);

        $extendInfo = $this->getExtendInfo();
        
        $collection = $itemCollection;
        
        foreach($itemCollection AS $datasetItem) {
            
            $itemData = $collection->getItemById($datasetItem->getId());
            
            $collection->removeItemByKey($datasetItem->getId());
            
            $disabled = "none";
            $checked = true;
            //process extend information
            if (!empty($extendInfo) && is_array($extendInfo)) {
                $itemData->addData($extendInfo[$datasetItem->getCode()]);
                if ($extendInfo[$datasetItem->getCode()]["disabled"]==true) {
                    $disabled = "disabled";
                    $checked = false;
                    $availabilityText = '<div style="color:#800;">version mismatch</div>';
                } else {
                    $availabilityText = '<div style="color:#080;"><b>available</b></div>';
                }
            }
            
            $itemData->setData("code", $datasetItem->getCode());
            $itemData->setData("id", $datasetItem->getId());
            $itemData->setData("itemCheckbox", $this->_addFieldset($datasetItem->getId(), $datasetItem->getCode(), $disabled, $checked));
            $itemData->setData("rollbackAvailability", $availabilityText);
            
            $collection->addItem($itemData);            
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Return input checkbox
     *
     * @param int $id
     * @param string $disabled
     * @param bool $checked
     * @return string
     */
    protected function _addFieldset($id, $code, $disabled, $checked)
    {
        $form = new Varien_Data_Form();

//        $form->addFieldset("checkbox_main_fieldset_" . $id, array())
               
        $form->addField("checkbox" .$id , "checkbox" , 
            array(
                'value' => $id,
                'name'  => "map[items][{$id}][dataset_item_id]",
                $disabled => true,
                'checked' => $checked 
            ) 
        );
        
        $form->addField("checkbox_hidden" .$id , "hidden", 
            array(
                'name'  => "map[items][{$id}][code]",
                'value' => $code,
            ) 
        );
        
        return $form->toHtml();                
    }
 
    
    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {

        $this->addColumn('itemCheckbox', array(
            'header'    => '',
            'index'     => 'itemCheckbox',
            'type'      => 'text',
            'truncate'  => 1000,
            'width'     => '20px'
        
        ));
                
        $this->addColumn('name', array(
            'header'    => 'Item Name',
            'index'     => 'name',
            'type'      => 'text',
        ));

        $this->addColumn('rollbackAvailability', array(
            'header'    => 'Rollback availability',
            'index'     => 'rollbackAvailability',
            'type'      => 'text',
        ));

        return $this;
    }

    /**
     * Retrieve currently edited staging object
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

}