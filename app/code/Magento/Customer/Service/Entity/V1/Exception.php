<?php
/**
 * Base service exception
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\Entity\V1;

class Exception extends \Exception
{
    /** Error codes */
    const CODE_UNKNOWN                              = 0;
    const CODE_ACCT_ALREADY_ACTIVE                  = 1;
    const CODE_INVALID_RESET_TOKEN                  = 2;
    const CODE_RESET_TOKEN_EXPIRED                  = 3;
    const CODE_EMAIL_NOT_FOUND                      = 4;
    const CODE_CONFIRMATION_NOT_NEEDED              = 5;
    const CODE_CUSTOMER_ID_MISMATCH                 = 6;
    const CODE_EMAIL_NOT_CONFIRMED                  = 7;
    const CODE_INVALID_EMAIL_OR_PASSWORD            = 8;
    const CODE_EMAIL_EXISTS                         = 9;
    const CODE_INVALID_RESET_PASSWORD_LINK_TOKEN    = 10;
    const CODE_ADDRESS_NOT_FOUND                    = 11;
    const CODE_INVALID_ADDRESS_ID                   = 12;
    const CODE_VALIDATION_FAILED                    = 13;
    const CODE_INVALID_CUSTOMER_ID                  = 14;
}
