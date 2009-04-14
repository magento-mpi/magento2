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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging entities tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Item extends Mage_Adminhtml_Block_Widget_Form
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

        $this->setFieldNameSuffix('staging[items]');

        $this->helper = Mage::helper('enterprise_staging');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Item
     */
    protected function _prepareForm()
    {
        $form          = new Varien_Data_Form();

        $staging       = $this->getStaging();
        $collection    = $staging->getItemsCollection();

        $fieldset = $form->addFieldset('staging_dataset_item', array('legend'=>Mage::helper('enterprise_staging')->__('Select Items to be merged')));

        $extendInfo = $this->getExtendInfo();
        
        foreach ($staging->getDatasetItemsCollection(true) as $datasetItem) {
            $_id = $datasetItem->getId();

            $disabled = "none";
            $note = "";
            
            //process extend information
            if (!empty($extendInfo) && is_array($extendInfo)) {
                if ($extendInfo[$datasetItem->getCode()]["disabled"]==true) {
                    $disabled = "disabled";
                    $note = '<div style="color:#900">'.$extendInfo[$datasetItem->getCode()]["note"] . "<div>"; 
                } else {
                    $note = '<div style="color:#090">'.$extendInfo[$datasetItem->getCode()]["note"] . "<div>"; 
                }
            }
            
            $fieldset->addField('dataset_item_id_'.$_id, 'checkbox',
                array(
                    'label'    => $datasetItem->getName(),
                    'name'     => "{$datasetItem->getId()}[dataset_item_id]",
                    'value'    => $datasetItem->getId(),
                    'checked'  => true,
                    $disabled  => true,                                                
                    'note'     => $note, 
                )
            );
            
            $fieldset->addField('staging_item_code_'.$_id, 'hidden',
                array(
                    'name'     => "{$datasetItem->getId()}[code]",
                    'value'    => $datasetItem->getCode()
                )
            );
        }

        $form->setFieldNameSuffix($this->getFieldNameSuffix());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Retrive staging object from setted data if not from registry
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }
}
