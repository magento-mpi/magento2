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
 * @package    Mage_Oscommerce
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce edit form
 *
 * @author     Kyaw Soe Lynn Maung <vincent@varien.com>
 */
class Mage_Oscommerce_Block_Adminhtml_Import_Edit_Tab_Run extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('oscommerce/convert/run.phtml');
    }

    /**
     * Prepares layout of block
     *
     */
    protected function _prepareLayout()
    {
        //$onclick = "submitAndReloadArea($('shipment_tracking_info').parentNode, '".$this->getSubmitUrl()."')";
        //$onclick = "runOsc()";
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('oscommerce')->__('Start Runing!'),
                    'class'   => 'run',
                    //'onclick' => $onclick,
                    'id'        => 'run_import'
                ))

        );

        $this->setChild('check_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('oscommerce')->__('Check requirements!'),
                    'class'   => 'run',
                    //'onclick' => $onclick,
                    'id'        => 'check_import'
                ))

        ); 

    }

    public function getImportId()
    {
        return Mage::registry('oscommerce_adminhtml_import')->getId();
    }

    /**
     * Retrieve run url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/run/', array('id'=>$this->getOscId()));
    }
        
    /**
     * Retrive run button html
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }    
    
    public function getCheckButtonHtml()
    {
        return $this->getChildHtml('check_button');
    }        

    public function getWebsiteOptionHtml()
    {

        $html  = '<select id="website" name="website">';
        $html .= '  <option value="">'.Mage::helper('oscommerce')->__('Select a website'). '</option>';
        $websites = Mage::app()->getWebsites();
        $websiteData = array();
        if ($websites) foreach($websites as $website) {
            $html .= '<option value='. $website->getId() . '>' . $website->getName() . '</option>';
        }
        $html .= '</select>';
        return $html;
    }
    
    public function getTimezoneOptionHtml()
    {
        $html  = '<select id="timezone" name="timezone">';
        $html .= '  <option value="">'.Mage::helper('oscommerce')->__('Select a timezone'). '</option>';
        $options = Mage::getModel('core/locale')->getOptionTimezones();
        if ($options) foreach($options as $option) {
            $html .= '<option value='. $option['value'] . '>' . $option['label'] . '</option>';
        }
        $html .= '</select>';
        return $html;
    	
    }
}
