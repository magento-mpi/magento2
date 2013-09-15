<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Bundle Selection Model
 *
 * @method \Magento\Bundle\Model\Resource\Selection _getResource()
 * @method \Magento\Bundle\Model\Resource\Selection getResource()
 * @method int getOptionId()
 * @method \Magento\Bundle\Model\Selection setOptionId(int $value)
 * @method int getParentProductId()
 * @method \Magento\Bundle\Model\Selection setParentProductId(int $value)
 * @method int getProductId()
 * @method \Magento\Bundle\Model\Selection setProductId(int $value)
 * @method int getPosition()
 * @method \Magento\Bundle\Model\Selection setPosition(int $value)
 * @method int getIsDefault()
 * @method \Magento\Bundle\Model\Selection setIsDefault(int $value)
 * @method int getSelectionPriceType()
 * @method \Magento\Bundle\Model\Selection setSelectionPriceType(int $value)
 * @method float getSelectionPriceValue()
 * @method \Magento\Bundle\Model\Selection setSelectionPriceValue(float $value)
 * @method float getSelectionQty()
 * @method \Magento\Bundle\Model\Selection setSelectionQty(float $value)
 * @method int getSelectionCanChangeQty()
 * @method \Magento\Bundle\Model\Selection setSelectionCanChangeQty(int $value)
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Model;

class Selection extends \Magento\Core\Model\AbstractModel
{
    /**
     * Catalog data
     *
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogData = null;

    /**
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Bundle_Model_Resource_Selection $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Bundle_Model_Resource_Selection $resource,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogData = $catalogData;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Bundle\Model\Resource\Selection');
        parent::_construct();
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Bundle\Model\Selection
     */
    protected function _beforeSave()
    {
        if (!$this->_catalogData->isPriceGlobal() && $this->getWebsiteId()) {
            $this->getResource()->saveSelectionPrice($this);

            if (!$this->getDefaultPriceScope()) {
                $this->unsSelectionPriceValue();
                $this->unsSelectionPriceType();
            }
        }
        parent::_beforeSave();
    }
}
