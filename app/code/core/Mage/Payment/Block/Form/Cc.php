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
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Payment_Block_Form_Cc extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('payment/form/cc.phtml');
    }
    
    /**
     * Retrieve payment configuration object
     *
     * @return Mage_Payment_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getSingleton('payment/config');
    }
    
    /**
     * Retrieve availables credit card types
     *
     * @return array
     */
    public function getCcAvailableTypes()
    {
        $types = $this->_getConfig()->getCcTypes();
        if ($this->getPaymentMethod()) {
            $availableTypes = $this->getPaymentMethod()->getConfigData('cctypes');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach ($types as $code=>$name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return $types;
    }
    
    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    public function getCcMonths()
    {
        return $this->_getConfig()->getMonths();
    }
    
    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    public function getCcYears()
    {
        return $this->_getConfig()->getYears();
    }
    
    public function hasVerification()
    {
        if ($this->getPaymentMethod()) {
            $configData = $this->getPaymentMethod()->getConfigData('useccv');
            if(is_null($configData)){
                return true;
            }
            return (bool) $configData;
        }
        return true;
    }
    
    
    
    /**
     * @todo change front cc templates
     */
    public function getCcTypes()
    {
        return array(
            ''=>__('Please select credit card type'),
            'AE'=>__('American Express'),
            'VI'=>__('Visa'),
            'MC'=>__('Master Card'),
            'DI'=>__('Discover'),
        );
    }

    public function getMonths()
    {
        return array(
            ''=>__('Month'),
             1=>'01-'.__('January'),
             2=>'02-'.__('February'),
             3=>'03-'.__('March'),
             4=>'04-'.__('April'),
             5=>'05-'.__('May'),
             6=>'06-'.__('June'),
             7=>'07-'.__('July'),
             8=>'08-'.__('August'),
             9=>'09-'.__('September'),
            10=>'10-'.__('October'),
            11=>'11-'.__('November'),
            12=>'12-'.__('December'),
        );
    }

    public function getYears()
    {
        for ($yearsArr=array(''=>__('Year')), $y1=date("Y"), $y=0; $y<10; $y++) {
            $yearsArr[$y1+$y] = $y1+$y;
        }
        return $yearsArr;
    }
}