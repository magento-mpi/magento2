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
 * Mock object for order shipping taxes collection
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Order_Tax_ShippingCollection
    extends Saas_PrintedTemplate_Model_Resource_Tax_Order_Shipping_Collection
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
     * Returns data for the order shipping taxes
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array(
            array (
                'shipping_tax_id' => '8',
                'order_id' => '-1',
                'code' => 'Test Tax Rates US',
                'title' => 'Test Tax Rates US',
                'percent' => '20.0000',
                'priority' => '2',
                'position' => '0',
                'is_tax_after_discount' => '0',

                'order_id' => '-1',
                'row_total' => '60.0000',
                'base_row_total' => '30.0000',
                'tax_amount' => '12.0000',
                'base_tax_amount' => '6.0000',
                'discount_amount' => '0.0000',
                'base_discount_amount' => '0.0000',
            ),
        );
    }
}
