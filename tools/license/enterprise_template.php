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

//if valid license and MAGE_LOCK_TIME not exist in the cache, lifetime of MAGE_LOCK_TIME was expired
if(!Mage::app()->loadCache('MAGE_LOCK_TIME')){
    define('TIME_BEFORE_NEXT_REQUEST', 86400);//24 hours is 86400 seconds
    define('LICENSE_SERVER_ADDRESS', 'https://license.magentocommerce.com/');//hardcoded server url
    Mage::app()->saveCache(time(), 'MAGE_LOCK_TIME',array(),TIME_BEFORE_NEXT_REQUEST);
    /**
     * request server
     */
    $reqRet=false;
    $data=array(
        'd'=>$_SERVER['HTTP_HOST'],
        'i'=>$_SERVER['SERVER_ADDR']
    );
    /**
     * use hardcoded server address
     */
    $url=LICENSE_SERVER_ADDRESS;
    $furl=(substr($url, 0, 4)!='http'?'http://':'').$url.'check.php?s='.urlencode(serialize($data));

    /**
     * Prepare request options
     */
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

    /**
     * Try to request
     */
    $ch = curl_init();
    curl_setopt_array($ch,$options);

    if(strpos($url,'https://')!==false){
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec ($ch);
        $err=curl_error($ch);
    }

    $reqRet = curl_exec ($ch);
    curl_close($ch);
}

// __require__
abstract class __ProtectorClass__ extends __ParentClass__
{
}

