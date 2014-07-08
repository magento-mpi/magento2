<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Attributes resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\GoogleShopping\Model\Resource;

use Magento\Framework\Parse\Zip;

class Attribute extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('googleshopping_attributes', 'id');
    }

    /**
     * Get regions by region ID
     *
     * @param int $regionId
     * @param string $postalCode
     * @return String[]
     */
    public function getRegionsByRegionId($regionId, $postalCode)
    {
        $regions = [];
        $adapter = $this->getReadConnection();
        $selectCSP = $adapter->select();
        $selectCSP->from(
            ['main_table' => $this->getTable('directory_country_region')],
            ['state' => 'main_table.code']
        )->where("main_table.region_id = $regionId");

        $dbResult = $adapter->fetchAll($selectCSP);
        if (!empty($dbResult)) {
            $state = $dbResult[0]['state'];
            $regions = Zip::parseRegions($state, $postalCode);
        }
        return $regions;
    }
}
