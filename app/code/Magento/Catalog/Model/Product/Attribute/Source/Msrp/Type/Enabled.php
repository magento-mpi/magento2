<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for 'msrp_enabled' product attribute
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type;

use Magento\Framework\DB\Ddl\Table;

class Enabled extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Enable MAP
     */
    const MSRP_ENABLE_YES = 1;

    /**
     * Disable MAP
     */
    const MSRP_ENABLE_NO = 0;

    /**
     * Get value from the store configuration settings
     */
    const MSRP_ENABLE_USE_CONFIG = 2;

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
     * Construct
     *
     * @param \Magento\Eav\Model\Resource\Entity\AttributeFactory $entityAttributeFactory
     * @param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\Eav\Model\Resource\Entity\AttributeFactory $entityAttributeFactory,
        \Magento\Core\Helper\Data $coreData
    ) {
        $this->_entityAttributeFactory = $entityAttributeFactory;
        $this->_coreData = $coreData;
    }

    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = array(
                array('label' => __('Yes'), 'value' => self::MSRP_ENABLE_YES),
                array('label' => __('No'), 'value' => self::MSRP_ENABLE_NO),
                array('label' => __('Use config'), 'value' => self::MSRP_ENABLE_USE_CONFIG)
            );
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
        $attributeCode = $this->getAttribute()->getAttributeCode();

        return [
            $attributeCode => [
                'unsigned' => false,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_SMALLINT,
                'length' => 1,
                'nullable' => true,
                'comment' => $attributeCode . ' column',
            ],
        ];
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->_entityAttributeFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
