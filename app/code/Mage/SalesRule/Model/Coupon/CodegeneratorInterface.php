<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_SalesRule
 * @copyright   {copyright}
 * @license     {license_link}
 */


interface Mage_SalesRule_Model_Coupon_CodegeneratorInterface
{
    /**
     * Retrieve generated code
     *
     * @return string
     */
    public function generateCode();

    /**
     * Retrieve delimiter
     *
     * @return string
     */
    public function getDelimiter();
}
