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
 * @package    Mage_Eway
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA protectedien (http://www.protectedien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract class for Eway API
 *
 */
abstract class Mage_Eway_Model_Api_Abstract extends Varien_Object
{    
  	public function getConfigData($key, $default=false)
    {
        if (!$this->hasData($key)) {
             $value = Mage::getStoreConfig('eway/eway_directapi/'.$key);
             if (is_null($value) || false===$value) {
                 $value = $default;
             }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }
    
    public function getSession()
    {
        return Mage::getSingleton('eway/session');
    }

    public function getUseSession()
    {
        if (!$this->hasData('use_session')) {
            $this->setUseSession(true);
        }
        return $this->getData('use_session');
    }

    public function getSessionData($key, $default=false)
    {
        if (!$this->hasData($key)) {
            if ($this->getSession()->hasData($key)) {
                $value = $this->getSession()->getData($key);
            } else {
                $value = $default;
            }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    public function setSessionData($key, $value)
    {
        if ($this->getUseSession()) {
            $this->getSession()->setData($key, $value);
        }
        $this->setData($key, $value);
        return $this;
    }

    public function getAmount()
    {
        return $this->getSessionData('amount');
    }

    public function setAmount($data)
    {
        return $this->setSessionData('amount', ($data*100));
    }
    
    public function getDebug()
    {
        return $this->getConfigData('debug_flag', true);
    }
    
    public function getUseccv()
    {
        if (!$this->hasData('useccv')) {
             $value = Mage::getStoreConfig('payment/eway_direct/useccv');
            $this->setData('useccv', $value);
        }
        return $this->getData('useccv');
    }
    
    public function getApiGatewayUrl()
    {
        $default = 'https://www.eway.com.au/gateway/xmlpayment.asp';
        return $this->getConfigData('api_url', $default);
    }
    
    public function getCustomerID()
    {
        return $this->getConfigData('customer_id');
    }

    public function setTransactionId($data)
    {
        return $this->setSessionData('transaction_id', $data);
    }

}
