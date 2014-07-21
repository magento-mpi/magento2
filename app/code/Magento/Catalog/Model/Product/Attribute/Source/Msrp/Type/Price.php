<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for 'msrp_display_actual_price_type' product attribute
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type;

class Price extends \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type
{
    /**
     * Get value from the store configuration settings
     */
    const TYPE_USE_CONFIG = '4';

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Entity attribute factory
     *
     * @var \Magento\Eav\Model\Resource\Entity\AttributeFactory
     */
    protected $_entityAttributeFactory;

    /**
     * Eav resource helper
     *
     * @var \Magento\Eav\Model\Resource\Helper
     */
    protected $_eavResourceHelper;

    /**
     * Construct
     *
     * @param \Magento\Eav\Model\Resource\Entity\AttributeFactory $entityAttributeFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Eav\Model\Resource\Helper $eavResourceHelper
     */
    public function __construct(
        \Magento\Eav\Model\Resource\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Eav\Model\Resource\Helper $eavResourceHelper
    ) {
        $this->_entityAttributeFactory = $entityAttributeFactory;
        $this->_coreData = $coreData;
        $this->_eavResourceHelper = $eavResourceHelper;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = parent::getAllOptions();
            $this->_options[] = array('label' => __('Use config'), 'value' => self::TYPE_USE_CONFIG);
        }
        return $this->_options;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeType = $this->getAttribute()->getBackendType();
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => $this->_eavResourceHelper->getDdlTypeByColumnType($attributeType),
                'nullable' => true,
            ],
        ];
    }

    /**
     * Retrieve select for flat attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->_entityAttributeFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
