<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EnterBoarding operation model
 *
 * @deprecated Use NVP implementation to work with permissions
 *
 * @category   Saas
 * @package    Saas_Paypal
 * @author     Magento Saas Team <core@magentocommerce.com>
 */
class Saas_Paypal_Model_Api_Soap_Operation_EnterBoarding extends Saas_Paypal_Model_Api_Soap_Operation_Abstract
{
    /**
     * Operation name for SOAP request.
     *
     * @var string
     */
    protected $_methodName = 'EnterBoarding';

    /**
     * Request array map for the operation between SOAP request and data in Api model.
     *
     * @var array
     */
    protected $_requestMap = array(
        'EnterBoardingRequest' => array(
            'EnterBoardingRequestDetails' => array(
                'ProgramCode' => 'program_code',
                'PartnerCustom' => 'partner_custom',
                'ProductList' => 'product_list',
                'MarketingCategory' => 'marketing_category'
            ),
            'Version' => 'version'
        )
    );

    /**
     * Response array map for the operation between SOAP response and data in Api model.
     *
     * @var array
     */
    protected $_responseMap = array(
        'Token' => 'boarding_token'
    );
}
