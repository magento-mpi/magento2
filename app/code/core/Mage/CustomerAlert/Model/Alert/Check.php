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
 * Customer alert check model
 *
 * @category   Mage
 * @package    Mage_CustomerAlert
 * @author     Vasily Selivanov <vasily@varien.com>
 */

class Mage_CustomerAlert_Model_Alert_Check extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->_init('customeralert/alert_check');
        parent::__construct();
        
    }
    
    public function loadIds($fetch)
    {
        return $this->getResource()->loadIds($this->getProductId(), $this->getStoreId(), $this->getType(), $fetch);
    }
    
    public function set($product_id, $store_id, $type)
    {
        $this->setProductId($product_id);
        $this->setStoreId($store_id);
        $this->setType($type);
        return $this;
    }
    
    public function setChecked($newValue, $oldValue, $date)
    {
        $this->setData('new_value',$newValue);
        $this->setData('old_value',$oldValue);
        $this->setData('date',$date);
        $this->getResource()->save($this);
        return $this;    
    }
    
}
