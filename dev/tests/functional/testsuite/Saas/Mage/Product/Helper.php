<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_PrintedTemplate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Saas_Mage_Product_Helper extends Enterprise_Mage_Product_Helper
{
    /**
    * Verifies if product with specified name presents on frontend page
    *
    * @param string $productName Product name
    * @return bool
    */
    public function isProductPresent($productName)
    {
        $this->addParameter('productName', $productName);
        return $this->controlIsPresent('pageelement', 'product_name_header');
    }
}
