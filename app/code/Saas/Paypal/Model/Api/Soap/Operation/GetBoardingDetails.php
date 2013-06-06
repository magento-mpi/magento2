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
 * GetBoardingDetails operation model
 *
 * @deprecated Use NVP implementation to work with permissions
 *
 * @category   Saas
 * @package    Saas_Paypal
 * @author     Magento Saas Team <core@magentocommerce.com>
 */
class Saas_Paypal_Model_Api_Soap_Operation_GetBoardingDetails extends Saas_Paypal_Model_Api_Soap_Operation_Abstract
{
    /**
     * GetBoardingDetails statuses
     *
     * @var string
     */
    const BOARDING_STATUS_PENDING   = 'Pending';
    const BOARDING_STATUS_COMPLETED = 'Completed';
    const BOARDING_STATUS_CANCELED  = 'Canceled';

    /**
     * Operation name for SOAP request.
     *
     * @var string
     */
    protected $_methodName = 'GetBoardingDetails';

    /**
     * Request array map for the operation between SOAP request and data in Api model.
     *
     * @var array
     */
    protected $_requestMap = array(
        'GetBoardingDetailsRequest' => array(
            'Token'     => 'boarding_token',
            'Version'   => 'version'
         )
    );

    /**
     * Response array map for the operation between SOAP response and data in Api model.
     *
     * @var array
     */
    protected $_responseMap = array(
        'GetBoardingDetailsResponseDetails' => array(
            'Status' => 'boarding_status',
            'AccountOwner' => array(
                'Payer' => 'boarding_account'
            )
        )
    );
}
