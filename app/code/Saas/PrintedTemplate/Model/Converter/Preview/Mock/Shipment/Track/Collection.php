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
 * Mock object for track numbers collection
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Converter_Preview_Mock_Shipment_Track_Collection
    extends Mage_Sales_Model_Resource_Order_Shipment_Track_Collection
{
    /**
     * Initialize collection with mock data
     */
    public function __construct(Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy)
    {
        parent::__construct($fetchStrategy);
        $this->_data = $this->_getMockData();
    }

    /**
     * Returns data for track numbers collection
     *
     * @return array
     */
    protected function _getMockData()
    {
        return array(
            array (
                'entity_id' => '5',
                'parent_id' => '-1',
                'weight' => NULL,
                'qty' => NULL,
                'order_id' => '8',
                'number' => '123132123',
                'description' => NULL,
                'title' => 'DHL',
                'carrier_code' => 'dhl',
                'created_at' => '2011-04-28 08:03:03',
                'updated_at' => '2011-04-28 08:03:03',
            ),
            array (
                'entity_id' => '6',
                'parent_id' => '-1',
                'weight' => NULL,
                'qty' => NULL,
                'order_id' => '8',
                'number' => '456456456',
                'description' => NULL,
                'title' => 'Federal Express',
                'carrier_code' => 'fedex',
                'created_at' => '2011-04-28 10:34:33',
                'updated_at' => '2011-04-28 10:34:33',
            ),
        );
    }
}
