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
 * @package    Mage_Protx
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Description goes here...
 *
 * @name       Mage_Protx_Model_Api_Abstract
 * @date       Fri Apr 04 12:46:34 EEST 2008
 */

class Mage_Protx_Model_Api_Abstract extends Varien_Object
{
    const MODE_SIMULATOR    = 'SIMULATOR';
    const MODE_TEST         = 'TEST';
    const MODE_LIVE         = 'LIVE';

    const PAYMENT_TYPE_PAYMENT      = 'PAYMENT';
    const PAYMENT_TYPE_DEFERRED     = 'DEFERRED';
    const PAYMENT_TYPE_AUTHENTICATE = 'AUTHENTICATE';
    const PAYMENT_TYPE_AUTHORISE    = 'AUTHORISE';


    public function getConfigData($key, $default=false)
    {
        if (!$this->hasData($key)) {
             $value = Mage::getStoreConfig('payment/protx_standard/'.$key);
             if (is_null($value) || false===$value) {
                 $value = $default;
             }
            $this->setData($key, $value);
        }
        return $this->getData($key);
    }

    /**
     *  Protocol version
     *
     *  @param    none
     *  @return	  String
     *  @date	  Mon Apr 07 18:53:40 EEST 2008
     */
    public function getVersion ()
    {
        return '2.22';
    }

    /**
     *
     *
     *  @param    none
     *  @return	  void
     *  @date	  Fri Apr 04 15:11:00 EEST 2008
     */
    public function getVendorName ()
    {
        return $this->getConfigData('vendor_name');
    }

    /**
     *
     *
     *  @param    none
     *  @return	  void
     *  @date	  Fri Apr 04 15:11:17 EEST 2008
     */
    public function getVendorPassword ()
    {
        return $this->getConfigData('vendor_password');
    }

    /**
     *
     *  @param    none
     *  @return	  void
     *  @date	  Tue Apr 08 11:32:48 EEST 2008
     */
    public function getPaymentType ()
    {
        return $this->getConfigData('payment_type');
    }

    /**
     *
     *  @param    none
     *  @return	  void
     *  @date	  Tue Apr 08 11:32:48 EEST 2008
     */
    public function getMode ()
    {
        return $this->getConfigData('mode');
    }

    /**
     *
     *  @param    none
     *  @return	  void
     *  @date	  Wed Apr 09 13:12:35 EEST 2008
     */
    public function getNewOrderStatus ()
    {
        return $this->getConfigData('order_status');
    }

    /**
     *
     *  @param    none
     *  @return	  void
     *  @date	  Tue Apr 08 11:32:48 EEST 2008
     */
    public function getDebug ()
    {
        return $this->getConfigData('debug_flag');
    }

    /**
     *
     *
     *  @param    none
     *  @return	  void
     *  @date	  Mon Apr 07 15:30:05 EEST 2008
     */
    public function getCryptKey ()
    {
        return $this->getVendorPassword();
    }

    /**
     *  Custom base64_encode()
     *
     *  @param    String
     *  @return	  String
     *  @date	  Mon Apr 07 15:30:05 EEST 2008
     */
    public function base64Encode($plain)
    {
        return base64_encode($plain);
    }

    /**
     *  Custom base64_dencode()
     *
     *  @param    String
     *  @return	  String
     *  @date	  Mon Apr 07 15:30:05 EEST 2008
     */
    public function base64Decode($scrambled)
    {
        // Fix plus to space conversion issue
        $scrambled = str_replace(" ","+",$scrambled);
        return base64_decode($scrambled);
    }


}