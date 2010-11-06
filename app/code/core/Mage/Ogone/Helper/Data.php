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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Ogone
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Ogone data helper
 */
class Mage_Ogone_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @deprecated after 1.4.2.0-beta1
     * @see self::HASH_ALGO_SHA1
     */
    const HASH_ALGO = 'sha1';

    /**
     * Parameters hashing context
     * @var string
     */
    const HASH_DIR_OUT = 'out';
    const HASH_DIR_IN = 'in';

    /**
     * Supported hashing algorithms
     * @var string
     */
    const HASH_SHA1 = 'sha1';
    const HASH_SHA256 = 'sha256';
    const HASH_SHA512 = 'sha512';

    /**
     * "OUT" hash string components, correspond to the "IN" signature in Ogone.
     * "Out" relative to Magento, "in" relative to Ogone.
     *
     * @var array
     */
    protected static $_outAllMap = array(
        'ACCEPTURL', 'ADDMATCH', 'ADDRMATCH', 'AIAIRNAME', 'AIAIRTAX', 'AICHDET', 'AICONJTI', 'AIEYCD', 'AIIRST',
        'AIPASNAME', 'AITIDATE', 'AITINUM', 'AITYPCH', 'AIVATAMNT', 'AIVATAPPL', 'ALIAS','ALIASOPERATION', 'ALIASUSAGE',
        'ALLOWCORRECTION', 'AMOUNT', 'AMOUNTHTVA', 'AMOUNTTVA', 'BACKURL', 'BGCOLOR', 'BRAND', 'BRANDVISUAL',
        'BUTTONBGCOLOR', 'BUTTONTXTCOLOR', 'CANCELURL', 'CARDNO', 'CATALOGURL', 'CAVV_3D', 'CAVVALGORITHM_3D', 'CERTID',
        'CHECK_AAV', 'CIVILITY', 'CN', 'COM', 'COMPLUS', 'COSTCENTER', 'COSTCODE', 'CREDITCODE', 'CUID', 'CURRENCY',
        'CVC', 'DATA', 'DATATYPE', 'DATEIN', 'DATEOUT', 'DECLINEURL', 'DISCOUNTRATE', 'ECI', 'ECOM_BILLTO_POSTAL_CITY',
        'ECOM_BILLTO_POSTAL_COUNTRYCODE', 'ECOM_BILLTO_POSTAL_NAME_FIRST', 'ECOM_BILLTO_POSTAL_NAME_LAST',
        'ECOM_BILLTO_POSTAL_POSTALCODE', 'ECOM_BILLTO_POSTAL_STREET_LINE1', 'ECOM_BILLTO_POSTAL_STREET_LINE2',
        'ECOM_BILLTO_POSTAL_STREET_NUMBER', 'ECOM_CONSUMERID', 'ECOM_CONSUMERORDERID', 'ECOM_CONSUMERUSERALIAS',
        'ECOM_PAYMENT_CARD_EXPDATE_MONTH', 'ECOM_PAYMENT_CARD_EXPDATE_YEAR', 'ECOM_PAYMENT_CARD_NAME',
        'ECOM_PAYMENT_CARD_VERIFICATION', 'ECOM_SHIPTO_COMPANY', 'ECOM_SHIPTO_DOB', 'ECOM_SHIPTO_ONLINE_EMAIL',
        'ECOM_SHIPTO_POSTAL_CITY', 'ECOM_SHIPTO_POSTAL_COUNTRYCODE', 'ECOM_SHIPTO_POSTAL_NAME_FIRST',
        'ECOM_SHIPTO_POSTAL_NAME_LAST', 'ECOM_SHIPTO_POSTAL_NAME_FIRST', 'ECOM_SHIPTO_POSTAL_POSTALCODE',
        'ECOM_SHIPTO_POSTAL_STREET_LINE1', 'ECOM_SHIPTO_POSTAL_STREET_LINE2', 'ECOM_SHIPTO_POSTAL_STREET_NUMBER',
        'ECOM_SHIPTO_TELECOM_FAX_NUMBER', 'ECOM_SHIPTO_TELECOM_PHONE_NUMBER', 'ECOM_SHIPTO_TVA', 'ED', 'EMAIL',
        'EXCEPTIONURL', 'EXCLPMLIST', 'FIRSTCALL', 'FLAG3D', 'FONTTYPE', 'FORCECODE1', 'FORCECODE2', 'FORCECODEHASH',
        'FORCEPROCESS', 'FORCETP', 'GENERIC_BL', 'GIROPAY_BL', 'GIROPAY_ACCOUNT_NUMBER', 'GIROPAY_BLZ',
        'GIROPAY_OWNER_NAME', 'GLOBORDERID', 'GUID', 'HDFONTTYPE', 'HDTBLBGCOLOR', 'HDTBLTXTCOLOR', 'HEIGHTFRAME',
        'HOMEURL', 'HTTP_ACCEPT', 'HTTP_USER_AGENT', 'INCLUDE_BIN', 'INCLUDE_COUNTRIES', 'INVDATE', 'INVDISCOUNT',
        'INVLEVEL', 'INVORDERID', 'ISSUERID', 'LANGUAGE', 'LEVEL1AUTHCPC', 'LIMITCLIENTSCRIPTUSAGE', 'LINE_REF',
        'LIST_BIN', 'LIST_COUNTRIES', 'LOGO', 'MERCHANTID', 'MODE', 'MTIME', 'MVER', 'NETAMOUNT', 'OPERATION',
        'ORDERID', 'ORIG', 'OR_INVORDERID', 'OR_ORDERID', 'OWNERADDRESS', 'OWNERADDRESS2', 'OWNERCTY', 'OWNERTELNO',
        'OWNERTOWN', 'OWNERZIP', 'PAIDAMOUNT', 'PARAMPLUS', 'PARAMVAR', 'PAYID', 'PAYMETHOD', 'PM', 'PMLIST',
        'PMLISTPMLISTTYPE', 'PMLISTTYPE', 'PMLISTTYPEPMLIST', 'PMTYPE', 'POPUP', 'POST', 'PSPID', 'PSWD', 'REF',
        'REFER', 'REFID', 'REFKIND', 'REF_CUSTOMERID', 'REF_CUSTOMERREF', 'REMOTE_ADDR', 'REQGENFIELDS','RTIMEOUT',
        'RTIMEOUTREQUESTEDTIMEOUT', 'SCORINGCLIENT', 'SETT_BATCH', 'SID', 'STATUS_3D', 'SUBSCRIPTION_ID', 'SUB_AM',
        'SUB_AMOUNT', 'SUB_COM', 'SUB_COMMENT', 'SUB_CUR', 'SUB_ENDDATE', 'SUB_ORDERID', 'SUB_PERIOD_MOMENT',
        'SUB_PERIOD_MOMENT_M', 'SUB_PERIOD_MOMENT_WW', 'SUB_PERIOD_NUMBER', 'SUB_PERIOD_NUMBER_D',
        'SUB_PERIOD_NUMBER_M', 'SUB_PERIOD_NUMBER_WW', 'SUB_PERIOD_UNIT', 'SUB_STARTDATE', 'SUB_STATUS', 'TAAL',
        'TBLBGCOLOR', 'TBLTXTCOLOR', 'TID', 'TITLE', 'TOTALAMOUNT', 'TP', 'TRACK2', 'TXTBADDR2', 'TXTCOLOR', 'TXTOKEN',
        'TXTOKENXTOKENPAYPAL', 'TYPE_COUNTRY', 'UCAF_AUTHENTIFICATION_DATA', 'UCAF_PAYMENT_CARD_CVC2',
        'UCAF_PAYMENT_CARD_EXPDATE_MONTH', 'UCAF_PAYMENT_CARD_EXPDATE_YEAR', 'UCAF_PAYMENT_CARD_NUMBER', 'USERID',
        'USERTYPE', 'VERSION', 'WBTU_MSISDN', 'WBTU_ORDERID', 'WEIGHTUNIT', 'WIN3DS', 'WITHROOT',
    );
    protected static $_outShortMap = array('ORDERID', 'AMOUNT', 'CURRENCY', 'PSPID', 'OPERATION',);

    /**
     * "IN" hash string components, correspond to the "OUT" signature in Ogone.
     * "In" relative to Magento, "out" relative to Ogone.
     *
     * @var array
     */
    protected static $_inAllMap = array(
        'AAVADDRESS', 'AAVCHECK', 'AAVZIP', 'ACCEPTANCE', 'ALIAS', 'AMOUNT', 'BRAND', 'CARDNO', 'CCCTY', 'CN',
        'COMPLUS', 'CREATION_STATUS', 'CURRENCY', 'CVCCHECK', 'DCC_COMMPERCENTAGE', 'DCC_CONVAMOUNT', 'DCC_CONVCCY',
        'DCC_EXCHRATE', 'DCC_EXCHRATESOURCE', 'DCC_EXCHRATETS', 'DCC_INDICATOR', 'DCC_MARGINPERCENTAGE',
        'DCC_VALIDHOURS', 'DIGESTCARDNO', 'ECI', 'ED', 'ENCCARDNO', 'IP', 'IPCTY', 'NBREMAILUSAGE', 'NBRIPUSAGE',
        'NBRIPUSAGE_ALLTX', 'NBRUSAGE', 'NCERROR', 'ORDERID', 'PAYID', 'PM', 'SCO_CATEGORY', 'SCORING','STATUS',
        'SUBSCRIPTION_ID', 'TRXDATE', 'VC',
    );
    protected static $_inShortMap = array(
        'ORDERID', 'CURRENCY', 'AMOUNT', 'PM', 'ACCEPTANCE', 'STATUS', 'CARDNO', 'PAYID', 'NCERROR', 'BRAND',
    );

    /**
     * @deprecated after 1.4.2.0-beta1
     * @see self::getHash()
     * @param array $data
     * @param string $key
     * @return string
     */
    public function shaCrypt($data, $key = '')
    {
        return '';
    }

    /**
     * @deprecated after 1.4.2.0-beta1
     * @see self::getHash()
     * @param array $data
     * @param string $hash
     * @param string $key
     * @return bool
     */
    public function shaCryptValidation($data, $hash, $key='')
    {
        return false;
    }

    /**
     * Create hash from provided data
     *
     * @param array $data
     * @param string $passPhrase
     * @param string $direction
     * @param bool|int $mapAllParams
     * @param string $algorithm
     * @return string
     * @throws Exception
     */
    public function getHash($data, $passPhrase, $direction, $mapAllParams = false, $algorithm = null)
    {
        // pick the right keys map depending on context
        if (self::HASH_DIR_OUT === $direction) {
            $hashMap = $mapAllParams ? '_outAllMap' : '_outShortMap';
        } elseif (self::HASH_DIR_IN === $direction) {
            $hashMap = $mapAllParams ? '_inAllMap' : '_inShortMap';
        } else {
            throw new Exception(sprintf('Unknown hashing context "%s".', $direction));
        }

        // collect non-empty data that maps and sort it alphabetically by key (uppercase)
        $collected = array();
        foreach ($data as $key => $value) {
            if (null !== $value && '' != $value) {
                $key = strtoupper($key);
                if (in_array($key, self::$$hashMap)) {
                    $collected[$key] = $value;
                }
            }
        }
        ksort($collected);

        if ($mapAllParams) {
            $nonHashed = $this->_concatenateAdvanced($collected, $passPhrase);
            if (null === $algorithm || self::HASH_SHA1 === $algorithm) {
                $algorithm = self::HASH_SHA256;
            }
        } else {
            $nonHashed = $this->_concatenateBasic($collected, $passPhrase, $hashMap);
            $algorithm = self::HASH_SHA1;
        }
        return strtoupper(hash($algorithm, $nonHashed));
    }

    /**
     * Transform collected data array to <value1><value2><...><passPhrase> according to the provided map
     *
     * @param array $data
     * @param string $passPhrase
     * @param string $hashMap
     * @return string
     */
    protected function _concatenateBasic($data, $passPhrase, $hashMap)
    {
        $result = '';
        foreach (self::$$hashMap as $key) {
            if (isset($data[$key])) {
                $result .= $data[$key];
            }
        }
        return $result . $passPhrase;
    }

    /**
     * Transform collected data array to <KEY>=<value><passPhrase>
     *
     * @param array $data
     * @param string $passPhrase
     * @return string
     */
    protected function _concatenateAdvanced($data, $passPhrase)
    {
        $result = '';
        foreach ($data as $key => $value) {
            $result .= "{$key}={$value}{$passPhrase}";
        }
        return $result;
    }
}
