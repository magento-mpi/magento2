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
 * @package    Mage_Cms
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer alert special price model
 *
 * @category   Mage
 * @package    Mage_CustomerAlert
 * @author     Vasily Selivanov <vasily@varien.com>
 */

class Mage_CustomerAlert_Model_Type_SpecialPrice extends Mage_CustomerAlert_Model_Type_Abstract
{
    public function __construct()
    {
    	$this->setType('special_price');
    	parent::__construct();
    }
    
    public function checkBefore(Mage_Catalog_Model_Product $oldProduct, Mage_Catalog_Model_Product $newProduct)
    {
    }
    
    public function checkAfter(Mage_Catalog_Model_Product $oldProduct, Mage_Catalog_Model_Product $newProduct)
    {
        if(!$oldProduct->getSpecialPrice()&&$newProduct->getSpecialPrice()){
            $this->addAlert(true, $newProduct->getSpecialPrice(), $oldProduct->getSpecialPrice());
        }
    }
    /**
     * Return checked text for accodion
     *
     * @return string
     */
    
    public function getAlertHappenedText()
    {
        return __('Special price was set to %s',$this->_newValue);
    }
    
    public function getAlertNotHappenedText()
    {
        return '';
    }
}
