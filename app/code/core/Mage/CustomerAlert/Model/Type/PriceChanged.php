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
 * Customer alert price is lowered model
 *
 * @category   Mage
 * @package    Mage_CustomerAlert
 * @authorÂ    Vasily Selivanov <vasily@varien.com>
 */

class Mage_CustomerAlert_Model_Type_PriceChanged extends Mage_CustomerAlert_Model_Type_Abstract
{
    public function __construct()
    {
    	$this->setType('price_is_changed');
    	#$this->setCheckedText = __('Price lowered from %f to %f');
    	parent::__construct();
    }
    
    public function checkAfter(Mage_Catalog_Model_Product $oldProduct, Mage_Catalog_Model_Product $newProduct)
    {
        if($oldProduct->getPrice()!=$newProduct->getPrice()){
            $this->addAlert(true, $newProduct->getPrice(), $oldProduct->getPrice());
        }
    }
    
    public function getAlertHappenedText()
    {
        return __('Price was changed from %s to %s at %s',$this->_oldValue,$this->_newValue,$this->_date);
    }
    
    public function getAlertNotHappenedText()
    {
        return '';
    }
    
}
