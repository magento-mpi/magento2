<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for 'msrp_display_actual_price_type' product attribute
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type;

class Price
    extends \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type
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
            $this->_options[] = array(
                'label' => __('Use config'),
                'value' => self::TYPE_USE_CONFIG
            );
        }
        return $this->_options;
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeType = $this->getAttribute()->getBackendType();
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => false,
            'default'   => null,
            'extra'     => null
        );

        if ($this->_coreData->useDbCompatibleMode()) {
            $column['type']     = $attributeType;
            $column['is_null']  = true;
        } else {
            $column['type']     = $this->_eavResourceHelper->getDdlTypeByColumnType($attributeType);
            $column['nullable'] = true;
        }

        return array($attributeCode => $column);
    }

    /**
     * Retrieve select for flat attribute update
     *
     * @param int $store
     * @return \Magento\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->_entityAttributeFactory->create()
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
