<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Data Helper
 *
 * @category   Mage
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Helper_Data extends Magento_Core_Helper_Abstract
{

    /**
     * Get Google Content Product ID
     *
     * @param int $productId
     * @param int $storeId
     * @return string
     */
    public function buildContentProductId($productId, $storeId)
    {
        return $productId . '_' . $storeId;
    }

    /**
     * Remove characters and words not allowed by Google Content in title and content (description).
     *
     * (to avoid "Expected response code 200, got 400.
     * Reason: There is a problem with the character encoding of this attribute")
     *
     * @param string $string
     * @return string
     */
    public function cleanAtomAttribute($string)
    {
        return Mage::helper('Magento_Core_Helper_String')
            ->substr(preg_replace('/[\pC¢€•—™°½]|shipping/ui', '', $string), 0, 3500);
    }

    /**
     * Normalize attribute's name.
     * The name has to be in lower case and the words are separated by symbol "_".
     * For instance: Meta Description = meta_description
     *
     * @param string $name
     * @return string
     */
    public function normalizeName($name)
    {
        return strtolower(preg_replace('/[\s_]+/', '_', $name));
    }

    /**
     * Parse Exception Response Body
     *
     * @param string $message Exception message to parse
     * @param null|Magento_Catalog_Model_Product $product
     * @return string
     */
    public function parseGdataExceptionMessage($message, $product = null)
    {
        $result = array();
        foreach (explode("\n", $message) as $row) {
            if (trim($row) == '') {
                continue;
            }

            if (strip_tags($row) == $row) {
                $row = preg_replace('/@ (.*)/', $this->__("See '\\1'"), $row);
                if (!is_null($product)) {
                    $row .= ' ' . $this->__("for product '%s' (in '%s' store)", $product->getName(), Mage::app()->getStore($product->getStoreId())->getName());
                }
                $result[] = $row;
                continue;
            }

            // parse not well-formatted xml
            preg_match_all('/(reason|field|type)=\"([^\"]+)\"/', $row, $matches);

            if (is_array($matches) && count($matches) == 3) {
                if (is_array($matches[1]) && count($matches[1]) > 0) {
                    $c = count($matches[1]);
                    for ($i = 0; $i < $c; $i++) {
                        if (isset($matches[2][$i])) {
                            $result[] = ucfirst($matches[1][$i]) . ': ' . $matches[2][$i];
                        }
                    }
                }
            }
        }
        return implode(". ", $result);
    }
}
