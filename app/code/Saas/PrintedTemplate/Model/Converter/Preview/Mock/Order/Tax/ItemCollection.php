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
 * Mock object for order item taxes collection
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Tax_ItemCollection
    extends Saas_PrintedTemplate_Model_Resource_Tax_Order_Item_Collection
{
    /**
     * Initialize order payment with mock data
     */
    public function __construct(Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy)
    {
        parent::__construct($fetchStrategy);
        $this->_data = $this->_getMockData();
    }

    /**
     * Returns data for the order item taxes
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array(
            array (
                'item_tax_id' => '28',
                'item_id' => '51',
                'code' => 'Tax US',
                'title' => 'Tax US',
                'percent' => '8.8800',
                'real_percent' => '8.8800',
                'priority' => '1',
                'is_tax_after_discount' => '0',
                'is_discount_on_incl_tax' => '0',

                'order_id' => '-1',
                'tax_amount' => '15.9800',
                'base_tax_amount' => '7.9900',
                'row_total' => '179.9800',
                'base_row_total' => '89.9900',
                'discount_amount' => '0',
                'base_discount_amount' => '0',
            ),
            array (
                'item_tax_id' => '29',
                'item_id' => '54',
                'code' => 'Test Tax Rates US',
                'title' => 'Test Tax Rates US',
                'percent' => '20.0000',
                'real_percent' => '20.0000',
                'priority' => '2',
                'is_tax_after_discount' => '0',
                'is_discount_on_incl_tax' => '0',

                'order_id' => '-1',
                'row_total' => '301.9800',
                'base_row_total' => '150.9900',
                'discount_amount' => '0',
                'base_discount_amount' => '0',
                'tax_amount' => '60.4000',
                'base_tax_amount' => '30.2000',
            ),
            array (
                'item_tax_id' => '30',
                'item_id' => '56',
                'code' => 'Test Tax Rates US',
                'title' => 'Test Tax Rates US',
                'percent' => '20.0000',
                'real_percent' => '20.0000',
                'priority' => '2',
                'is_tax_after_discount' => '0',
                'is_discount_on_incl_tax' => '0',

                'order_id' => '-1',
                'row_total' => '1340.0000',
                'base_row_total' => '670.0000',
                'discount_amount' => '0',
                'base_discount_amount' => '0',
                'tax_amount' => '268.0000',
                'base_tax_amount' => '134.0000',
            ),
        );
    }
}
