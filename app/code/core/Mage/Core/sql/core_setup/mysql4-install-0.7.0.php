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

$this->addConfigField('advanced', 'Advanced', array(
	'frontend_type'=>'text',
	'sort_order'=>'100',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('advanced/datashare', 'Datasharing', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'0',
	), '');
$this->addConfigField('advanced/datashare/default', 'Default', array(
	'frontend_type'=>'multiselect',
	'backend_model'=>'adminhtml/system_config_backend_datashare',
	'source_model'=>'adminhtml/system_config_source_store',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0,1,5,6');
$this->addConfigField('advanced/datashare/default', 'Default', array(
	'frontend_type'=>'multiselect',
	'backend_model'=>'adminhtml/system_config_backend_datashare',
	'source_model'=>'adminhtml/system_config_source_store',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1,5,6');
$this->addConfigField('advanced/modules_disable_output', 'Disable modules output', array(
	'frontend_type'=>'text',
	'frontend_model'=>'adminhtml/system_config_form_fieldset_modules_disableOutput',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');

$this->addConfigField('allow', 'Locale Settings', array(
	'frontend_type'=>'text',
	'sort_order'=>'31',
	'show_in_default'=>'1',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), '');
$this->addConfigField('allow/currency', 'Currency', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), '');
$this->addConfigField('allow/currency/code', 'Allowed Currencies', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'adminhtml/system_config_source_locale_currency_all',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'0',
	'show_in_store'=>'0',
	), 'ADP,AED,AFN,ALL,AMD,ANG,AOA,ARA,ARS,ATS,AUD,AWG,AZN,BAD,BAM,BBD,BDT,BEF,BGN,BHD,BIF,BMD,BND,BOB,BOP,BOV,BRC,BRL,BRN,BRR,BSD,BTN,BUK,BWP,BYR,BZD,CAD,CDF,CHE,CHF,CHW,CLF,CLP,CNY,COP,COU,CRC,CUP,CVE,CYP,CZK,DEM,DJF,DKK,DOP,DZD,ECS,EEK,EGP,EQE,ERN,ESP,ETB,EUR,FIM,FJD,FKP,FRF,GBP,GEK,GHS,GIP,GMD,GNF,GNS,GQE,GRD,GTQ,GWE,GWP,GYD,HKD,HNL,HRD,HRK,HTG,HUF,IDR,IEP,ILS,INR,IQD,INR,IQD,IRR,ISK,ITL,JMD,JOD,JPY,KES,KGS,KHR,KMF,KPW,KRW,KWD,KYD,KZT,LAK,LBP,LKR,LRD,LSL,LSM,LTL,LTT,LUF,LVL,LYD,MAD,MAF,MDL,MGA,MGF,MKD,MLF,MMK,MNT,MOP,MRO,MTL,MTP,MUR,MVR,MWK,MXN,MYR,MZE,MZN,NAD,NGN,NIC,NLG,NOK,NPR,NZD,OMR,PAB,PEI,PES,PGK,PHP,PKR,PLN,PTE,PYG,QAR,RHD,RON,RSD,RUB,RWF,SAR,SBD,SCR,SDG,SEK,SGD,SHP,SIT,SKK,SLL,SOS,SRD,SRG,STD,SVC,SYP,SZL,THB,TJR,TJS,TMM,TND,TOP,TPE,TRY,TTD,TWD,TZS,UAH,UGX,USD,UYU,UZS,VEB,VND,VUV,WST,XCD,YER,ZAR,ZMK,ZRN,ZRZ,ZWD');

$this->addConfigField('currency', 'Currency Setup', array(
	'frontend_type'=>'text',
	'sort_order'=>'31',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('currency/import', 'Import Settings', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('currency/import/enabled', 'Enabled', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('currency/import/error_email', 'Notification Email', array(
	'frontend_type'=>'text',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('currency/import/frequency', 'Frequency', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_cron_frequency',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('currency/import/service', 'Service', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_currency_service',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('currency/import/time', 'Start Time', array(
	'frontend_type'=>'time',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('currency/options', 'Currency options', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('currency/options/allow', 'Allowed currencies', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'adminhtml/system_config_source_currency',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'USD,EUR');
$this->addConfigField('currency/options/base', 'Base currency', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_currency',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'0',
	), 'USD');
$this->addConfigField('currency/options/default', 'Display default currency', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_currency',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'USD');

$this->addConfigField('design', 'Design', array(
	'frontend_type'=>'text',
	'sort_order'=>'31',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/package', 'Package', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/package/name', 'Current package name', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'default');
$this->addConfigField('design/package/name', 'Current package name', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'default');
$this->addConfigField('design/package/name', 'Current package name', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'default');
$this->addConfigField('design/theme', 'Themes', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/default', 'Default', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/default', 'Default', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/layout', 'Layout', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/layout', 'Layout', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/skin', 'Skin (Images / CSS)', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/skin', 'Skin (Images / CSS)', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/template', 'Templates', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/template', 'Templates', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/translate', 'Translations', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('design/theme/translate', 'Translations', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');

$this->addConfigField('dev', 'Developer', array(
	'frontend_type'=>'text',
	'sort_order'=>'900',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('dev/debug', 'Debug', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('dev/debug/profiler', 'Profiler', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '0');
$this->addConfigField('dev/mode', 'Operating mode', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('dev/mode/checksum', 'Validate config checksums', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
	
$this->addConfigField('general', 'General', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('general/country', 'Countries options', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('general/country/allow', 'Allow countries', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'adminhtml/system_config_source_country',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '38,220,223');
$this->addConfigField('general/country/allow', 'Allow countries', array(
	'frontend_type'=>'multiselect',
	'source_model'=>'adminhtml/system_config_source_country',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'AF,AL,DZ,AS,AD,AO,AI,AQ,AG,AR,AM,AW,AU,AT,AZ,BS,BH,BD,BB,BY,BE,BZ,BJ,BM,BT,BO,BA,BW,BV,BR,IO,VG,BN,BG,BF,BI,KH,CM,CA,CV,KY,CF,TD,CL,CN,CX,CC,CO,KM,CG,CK,CR,HR,CU,CY,CZ,DK,DJ,DM,DO,EC,EG,SV,GQ,ER,EE,ET,FK,FO,FJ,FI,FR,GF,PF,TF,GA,GM,GE,DE,GH,GI,GR,GL,GD,GP,GU,GT,GN,GW,GY,HT,HM,HN,HK,HU,IS,IN,ID,IR,IQ,IE,IL,IT,CI,JM,JP,JO,KZ,KE,KI,KW,KG,LA,LV,LB,LS,LR,LY,LI,LT,LU,MO,MK,MG,MW,MY,MV,ML,MT,MH,MQ,MR,MU,YT,FX,MX,FM,MD,MC,MN,MS,MA,MZ,MM,NA,NR,NP,NL,AN,NC,NZ,NI,NE,NG,NU,NF,KP,MP,NO,OM,PK,PW,PA,PG,PY,PE,PH,PN,PL,PT,PR,QA,RE,RO,RU,RW,SH,KN,LC,PM,VC,WS,SM,ST,SA,SN,SC,SL,SG,SK,SI,SB,SO,ZA,GS,KR,ES,LK,SD,SR,SJ,SZ,SE,CH,SY,TW,TJ,TZ,TH,TG,TK,TO,TT,TN,TR,TM,TC,TV,VI,UG,UA,AE,GB,US,UM,UY,UZ,VU,VA,VE,VN,WF,EH,YE,ZM,ZW');
$this->addConfigField('general/country/default', 'Default country', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_country',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'US');
$this->addConfigField('general/country/default', 'Default country', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_country',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '223');
$this->addConfigField('general/locale', 'Locale options', array(
	'frontend_type'=>'text',
	'sort_order'=>'8',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('general/locale/code', 'Locale', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_locale',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'en_US');
$this->addConfigField('general/locale/timezone', 'Timezone', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_locale_timezone',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'America/Los_Angeles');

$this->addConfigField('system', 'System', array(
	'frontend_type'=>'text',
	'sort_order'=>'80',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('system/filesystem', 'Filesystem', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('system/filesystem/base', 'Base directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{root_dir}}');
$this->addConfigField('system/filesystem/cache_config', 'Config cache directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{var_dir}}/cache/config');
$this->addConfigField('system/filesystem/cache_layout', 'Layout cache directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{var_dir}}/cache/layout');
$this->addConfigField('system/filesystem/code', 'Code pools root directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{app_dir}}/code');
$this->addConfigField('system/filesystem/etc', 'Configuration directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{app_dir}}/etc');
$this->addConfigField('system/filesystem/layout', 'Layout files directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'6',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{app_dir}}/design/frontend/default/layout/default');
$this->addConfigField('system/filesystem/media', 'Media files directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'7',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{root_dir}}/media');
$this->addConfigField('system/filesystem/session', 'Session files directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'8',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{var_dir}}/session');
$this->addConfigField('system/filesystem/skin', 'Skin directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'9',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{root_dir}}/skin');
$this->addConfigField('system/filesystem/template', 'Template directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'10',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{app_dir}}/design/frontend/default/template/default');
$this->addConfigField('system/filesystem/translate', 'Translactions directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'11',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{app_dir}}/design/frontend/default/translate');
$this->addConfigField('system/filesystem/upload', 'Upload directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'12',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{root_dir}}/media/upload');
$this->addConfigField('system/filesystem/var', 'Var (temporary files) directory', array(
	'frontend_type'=>'text',
	'sort_order'=>'13',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{var_dir}}');
$this->addConfigField('system/smtp', 'SMTP settings (Windows server only)', array(
	'frontend_type'=>'text',
	'sort_order'=>'14',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('system/smtp/host', 'Host', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'localhost');
$this->addConfigField('system/smtp/port', 'Port (25)', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '25');

$this->addConfigField('trans_email', 'Store Email Addresses', array(
	'frontend_type'=>'text',
	'sort_order'=>'101',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('trans_email/ident_custom1', 'Custom email 1', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('trans_email/ident_custom1/email', 'Sender email', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'custom1@example.com');
$this->addConfigField('trans_email/ident_custom1/name', 'Sender name', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Custom 1');
$this->addConfigField('trans_email/ident_custom2', 'Custom email 2', array(
	'frontend_type'=>'text',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('trans_email/ident_custom2/email', 'Sender email', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'custom2@example.com');
$this->addConfigField('trans_email/ident_custom2/name', 'Sender name', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Custom 2');
$this->addConfigField('trans_email/ident_general', 'General contact', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('trans_email/ident_general/email', 'Sender email', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'owner@example.com');
$this->addConfigField('trans_email/ident_general/name', 'Sender name', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'General');
$this->addConfigField('trans_email/ident_sales', 'Sales representative', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('trans_email/ident_sales/email', 'Sender email', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'sales@example.com');
$this->addConfigField('trans_email/ident_sales/name', 'Sender name', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Sales');
$this->addConfigField('trans_email/ident_support', 'Customer support', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('trans_email/ident_support/email', 'Sender email', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'support@example.com');
$this->addConfigField('trans_email/ident_support/name', 'Sender name', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'Customer support');

$this->addConfigField('web', 'Web', array(
	'frontend_type'=>'text',
	'sort_order'=>'20',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/cookie', 'Cookie management', array(
	'frontend_type'=>'text',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/cookie/cookie_domain', 'Cookie Domain', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/cookie/cookie_domain', 'Cookie Domain', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/cookie/cookie_lifetime', 'Cookie Lifetime', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/cookie/cookie_lifetime', 'Cookie Lifetime', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/cookie/cookie_path', 'Cookie Path', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/cookie/cookie_path', 'Cookie Path', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/default', 'Default URLs', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/default/cms_home_page', 'CMS Home Page', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_cms_page',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'home');
$this->addConfigField('web/default/cms_no_route', 'CMS No Route Page', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_cms_page',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'no-route');
$this->addConfigField('web/default/front', 'Default web url', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'cms');
$this->addConfigField('web/default/front', 'Default web url', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'cms');
$this->addConfigField('web/default/no_route', 'Default no-route url', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'cms/index/noRoute');
$this->addConfigField('web/default/no_route', 'Default no-route url', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'cms/index/noRoute');
$this->addConfigField('web/default/show_cms_breadcrumbs', 'Show breadcrumbs for CMS pages', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'5',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '1');
$this->addConfigField('web/secure', 'Secure', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/secure/base_path', 'Base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/');
$this->addConfigField('web/secure/base_path', 'Base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/');
$this->addConfigField('web/secure/host', 'Host', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{host}}');
$this->addConfigField('web/secure/host', 'Host', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'magento-yuriy.kiev-dev');
$this->addConfigField('web/secure/port', 'Port', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{port}}');
$this->addConfigField('web/secure/port', 'Port', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '80');
$this->addConfigField('web/secure/protocol', 'Protocol', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_web_protocol',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'http');
$this->addConfigField('web/secure/protocol', 'Protocol', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_web_protocol',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'http');
$this->addConfigField('web/unsecure', 'Unsecure', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/unsecure/base_path', 'Base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/');
$this->addConfigField('web/unsecure/base_path', 'Base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/');
$this->addConfigField('web/unsecure/host', 'Host', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{host}}');
$this->addConfigField('web/unsecure/host', 'Host', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'magento-yuriy.kiev-dev');
$this->addConfigField('web/unsecure/port', 'Port', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '{{port}}');
$this->addConfigField('web/unsecure/port', 'Port', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '80');
$this->addConfigField('web/unsecure/protocol', 'Protocol', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_web_protocol',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'http');
$this->addConfigField('web/unsecure/protocol', 'Protocol', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_web_protocol',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), 'http');
$this->addConfigField('web/url', 'URLs', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web/url/js', 'Js base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/js/');
$this->addConfigField('web/url/js', 'Js base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/js/');
$this->addConfigField('web/url/media', 'Media base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/media/');
$this->addConfigField('web/url/media', 'Media base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'2',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/media/');
$this->addConfigField('web/url/skin', 'Skin base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/skin/');
$this->addConfigField('web/url/skin', 'Skin base url', array(
	'frontend_type'=>'text',
	'sort_order'=>'3',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/skin/');
$this->addConfigField('web/url/upload', 'Upload files URL', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/media/upload/');
$this->addConfigField('web/url/upload', 'Upload files URL', array(
	'frontend_type'=>'text',
	'sort_order'=>'4',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '/media/upload/');
$this->addConfigField('web/url/use_relative', 'Use relative paths in links', array(
	'frontend_type'=>'select',
	'source_model'=>'adminhtml/system_config_source_yesno',
	'sort_order'=>'1',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
$this->addConfigField('web_track', 'Web tracking', array(
	'frontend_type'=>'text',
	'sort_order'=>'31',
	'show_in_default'=>'1',
	'show_in_website'=>'1',
	'show_in_store'=>'1',
	), '');
