<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_WebsiteRestriction
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Restriction modes dictionary
 *
 */
class Enterprise_WebsiteRestriction_Model_Mode
{
    const ALLOW_NONE     = 0;
    const ALLOW_LOGIN    = 1;
    const ALLOW_REGISTER = 2;

    const HTTP_200 = 0;
    const HTTP_503 = 1;
    const HTTP_302_LOGIN   = 0;
    const HTTP_302_LANDING = 1;
}
