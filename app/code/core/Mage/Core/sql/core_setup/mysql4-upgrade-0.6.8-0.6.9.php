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

$this->addConfigField('allow', 'Locale Settings', array(
    'show_in_website'=>0,
    'show_in_store'=>0,
));
$this->addConfigField('allow/currency', 'Currency', array(
    'show_in_website'=>0,
    'show_in_store'=>0,
));
$this->addConfigField('allow/currency/code', 'Allowed Currencies', array(
    'frontend_type'=>'multiselect',
    'source_model'=>'adminhtml/system_config_source_locale_currency_all',
    'show_in_website'=>0,
    'show_in_store'=>0,
));
$this->setConfigData('allow/currency/code', 'ADP,AED,AFN,ALL,AMD,ANG,AOA,ARA,ARS,ATS,AUD,AWG,AZN,BAD,BAM,BBD,BDT,BEF,BGN,BHD,BIF,BMD,BND,BOB,BOP,BOV,BRC,BRL,BRN,BRR,BSD,BTN,BUK,BWP,BYR,BZD,CAD,CDF,CHE,CHF,CHW,CLF,CLP,CNY,COP,COU,CRC,CUP,CVE,CYP,CZK,DEM,DJF,DKK,DOP,DZD,ECS,EEK,EGP,EQE,ERN,ESP,ETB,EUR,FIM,FJD,FKP,FRF,GBP,GEK,GHS,GIP,GMD,GNF,GNS,GQE,GRD,GTQ,GWE,GWP,GYD,HKD,HNL,HRD,HRK,HTG,HUF,IDR,IEP,ILS,INR,IQD,INR,IQD,IRR,ISK,ITL,JMD,JOD,JPY,KES,KGS,KHR,KMF,KPW,KRW,KWD,KYD,KZT,LAK,LBP,LKR,LRD,LSL,LSM,LTL,LTT,LUF,LVL,LYD,MAD,MAF,MDL,MGA,MGF,MKD,MLF,MMK,MNT,MOP,MRO,MTL,MTP,MUR,MVR,MWK,MXN,MYR,MZE,MZN,NAD,NGN,NIC,NLG,NOK,NPR,NZD,OMR,PAB,PEI,PES,PGK,PHP,PKR,PLN,PTE,PYG,QAR,RHD,RON,RSD,RUB,RWF,SAR,SBD,SCR,SDG,SEK,SGD,SHP,SIT,SKK,SLL,SOS,SRD,SRG,STD,SVC,SYP,SZL,THB,TJR,TJS,TMM,TND,TOP,TPE,TRY,TTD,TWD,TZS,UAH,UGX,USD,UYU,UZS,VEB,VND,VUV,WST,XCD,YER,ZAR,ZMK,ZRN,ZRZ,ZWD');