<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pricing
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pricing\Object;

/**
 * //@TODO Templates invoke methods that are not defined in the interface:
 *  getProductUrl():
 *      /app\code\Magento\Catalog\view\frontend\product\price\final_price.phtml
 *      /app\code\Magento\Catalog\view\frontend\product\price\msrp_item.phtml
 *
 *  getId() - /app\code\Magento\Catalog\view\frontend\product\price\final_price.phtml
 *  getMsrp() - /app\code\Magento\Catalog\view\frontend\product\price\msrp_item.phtml
 */
interface SaleableInterface
{
    /**
     * @return \Magento\Pricing\PriceInfoInterface
     */
    public function getPriceInfo();

    /**
     * @return string
     */
    public function getTypeId();

    /**
     * @return int
     */
    public function getId();

    /**
     * @return float
     */
    public function getQty();
}
