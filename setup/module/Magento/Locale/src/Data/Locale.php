<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Locale\Data;

class Locale implements ListInterface
{
    /**
     * @var array
     */
    protected $data = [
        'af_ZA',
        'ar_DZ', 'ar_EG', 'ar_KW', 'ar_MA', 'ar_SA',
        'az_AZ',
        'be_BY',
        'bg_BG',
        'bn_BD',
        'bs_BA',
        'ca_ES',
        'cs_CZ',
        'cy_GB',
        'da_DK',
        'de_AT', 'de_CH', 'de_DE',
        'el_GR',
        'en_AU', 'en_CA', 'en_GB', 'en_IE', 'en_NZ', 'en_US',
        'es_AR', 'es_CL', 'es_CO', 'es_CR', 'es_ES', 'es_MX', 'es_PA', 'es_PE', 'es_VE',
        'et_EE',
        'fa_IR',
        'fi_FI',
        'fil_PH',
        'fr_CA', 'fr_FR',
        'gl_ES',
        'gu_IN',
        'he_IL',
        'hi_IN',
        'hr_HR',
        'hu_HU',
        'id_ID',
        'is_IS',
        'it_CH', 'it_IT',
        'ja_JP',
        'ka_GE',
        'km_KH',
        'ko_KR',
        'lo_LA',
        'lt_LT',
        'lv_LV',
        'mk_MK',
        'mn_MN',
        'ms_MY',
        'nb_NO',
        'nl_NL',
        'nn_NO',
        'pl_PL',
        'pt_BR', 'pt_PT',
        'ro_RO',
        'ru_RU',
        'sk_SK',
        'sl_SI',
        'sq_AL',
        'sr_RS',
        'sv_SE',
        'sw_KE',
        'th_TH',
        'tr_TR',
        'uk_UA',
        'vi_VN',
        'zh_CN', 'zh_HK', 'zh_TW',
    ];

    /**
     * Retrieve list of locales
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
} 