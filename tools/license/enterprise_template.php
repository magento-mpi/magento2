<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_Enterprise
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

 /**
 * MAGE_LOCK_TIME - cache variable has some data
 * MAGE_LOCK_INVALIDS - cache variable, counter of invalid requests
 * MAGE_LOCK_TIME_WAIT - cache variable, counter of invalid requests
 */

$licenseNotValid=false;
if(ioncube_license_has_expired()){
    $licenseNotValid=true;
}
//if valid license and MAGE_LOCK_TIME not exist in the cache, lifetime of MAGE_LOCK_TIME was expired
if(!$licenseNotValid && !Mage::app()->loadCache('MAGE_LOCK_TIME')){
    define('REQUEST_INVALID_COUNT', 2);
    define('TIME_BEFORE_NEXT_REQUEST', 50);//24 hours 86400
    define('TIME_REQUEST_RETRY', 25);//3 hours 10800 must be less then TIME_BEFORE_NEXT_REQUEST
    define('REQUEST_TIMEOUT', 5);//request timeout in seconds

    $invreq=intval(Mage::app()->loadCache('MAGE_LOCK_INVALIDS'));
    if($invreq<REQUEST_INVALID_COUNT){
        Mage::app()->saveCache(time(), 'MAGE_LOCK_TIME',array(),TIME_BEFORE_NEXT_REQUEST);
    }
    /**
     * request server
     */
    $reqRet=false;
    if(!Mage::app()->loadCache('MAGE_LOCK_TIME_WAIT')){
        Mage::app()->saveCache(time(), 'MAGE_LOCK_TIME_WAIT',array(),TIME_REQUEST_RETRY);
        //prepare request data
        $license_properties=ioncube_license_properties();
        $data=array(
            'l'=>$license_properties['licenseid'],
            'd'=>$_SERVER['HTTP_HOST'],
            'i'=>$_SERVER['SERVER_ADDR'],
        );
        if($license_properties['ip']){
            if(is_array($license_properties['ip'])){
                $url=$license_properties['ip'][rand(0, count($license_properties['ip']))];
            }else{
                $url=$license_properties['ip'];
            }
        }else{
            //hardcoded ip
            //$url='support.varien.com/do/';
            $url='192.168.0.222';
        }
        $furl='http://'.$url.'?s='.urlencode(serialize($data));

        // try to request
        $ch = curl_init();
        $options = array
        (
            CURLOPT_HEADER=>0,
            CURLOPT_RETURNTRANSFER=>1,
            CURLOPT_BINARYTRANSFER=>1,
            //CURLOPT_FOLLOWLOCATION=>1, //go to redirected page
            //CURLOPT_TIMEOUT_MS=>10, //set nimeout in milliseconds
            CURLOPT_TIMEOUT=>REQUEST_TIMEOUT,
            CURLOPT_URL=>$furl,
        );
        curl_setopt_array($ch,$options);
        $reqRet = curl_exec ($ch);
        curl_close($ch);
    }
    if($reqRet){
        Mage::app()->saveCache(0, 'MAGE_LOCK_INVALIDS',array());
    }else{
        $invreq++;
        Mage::app()->saveCache($invreq, 'MAGE_LOCK_INVALIDS',array());
        if(!($invreq<REQUEST_INVALID_COUNT)){
            //throw new Exception('Your license has expired', '1');
            $licenseNotValid=true;
       }
    }
}
if($licenseNotValid){
    /**
     * Ancestor class for not valid license
     */
    abstract class __ProtectorClass__ extends Varien_Object
    {
        public function __call($name,  $arguments) {
            return false;
        }
        public static function __callStatic($name,  $arguments) {
            return false;
        }
        public function __get($name) {
            return false;
        }
        public function __set($name, $value) {
            return false;
        }
    }/**/
}else{
    /**
     * Ancestor class
     */
    // __require__
    abstract class __ProtectorClass__ extends __ParentClass__
    {
    }
}
