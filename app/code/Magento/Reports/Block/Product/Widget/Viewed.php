<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Reports Recently Viewed Products Widget
 *
 * @category   Magento
 * @package    Magento_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Reports_Block_Product_Widget_Viewed
    extends Magento_Reports_Block_Product_Viewed
    implements Magento_Widget_Block_Interface
{
    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->addColumnCountLayoutDepend('one_column', 5)
            ->addColumnCountLayoutDepend('two_columns_left', 4)
            ->addColumnCountLayoutDepend('two_columns_right', 4)
            ->addColumnCountLayoutDepend('three_columns', 3);
        $this->addPriceBlockType(
            'bundle',
            'Magento_Bundle_Block_Catalog_Product_Price',
            'catalog/product/price.phtml'
        );
    }
}
