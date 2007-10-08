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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Core data helper
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_crypt;
    
    /**
     * Convert and format price value for current application store
     *
     * @param   float $value
     * @param   bool $format
     * @return  mixed
     */
    public static function currency($value, $format=true)
    {
        try {
            $value = Mage::app()->getStore()->convertPrice($value, $format);
        }
        catch (Exception $e){
            $value = $e->getMessage();
        }
    	return $value;
    }
    
    public function date($date=null, $format='short', $showTime=false)
    {
        
    }
    
    /**
     * Encrypt data using application key
     *
     * @param   string $data
     * @return  string
     */
    public function encrypt($data)
    {
        if (!Mage::app()->isInstalled()) {
            return $data;
        }
        $result = base64_encode($this->_getCrypt()->encrypt($data));
        return $result;
    }
    
    /**
     * Decrypt data using application key
     *
     * @param   string $data
     * @return  string
     */
    public function decrypt($data)
    {
        if (!Mage::app()->isInstalled()) {
            return $data;
        }
        $result = trim($this->_getCrypt()->decrypt(base64_decode($data)));
        return $result;
    }
    
    protected function _getCrypt()
    {
        if (!$this->_crypt) {
            $key = (string)Mage::getConfig()->getNode('global/crypt/key');
            $this->_crypt = Varien_Crypt::factory()->init($key);
        }
        return $this->_crypt;
    }
}
