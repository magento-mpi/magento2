<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftCard\Model\Source;

use Magento\Framework\DB\Ddl\Table;

class Type extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Eav entity attribute factory
     *
     * @var \Magento\Eav\Model\Resource\Entity\AttributeFactory
     */
    protected $_eavAttributeFactory;

    /**
     * @param \Magento\Eav\Model\Resource\Entity\AttributeFactory $eavAttributeFactory
     * @param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\Eav\Model\Resource\Entity\AttributeFactory $eavAttributeFactory,
        \Magento\Core\Helper\Data $coreData
    ) {
        $this->_eavAttributeFactory = $eavAttributeFactory;
        $this->_coreData = $coreData;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = [];
        foreach ($this->_getValues() as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }

        return $result;
    }

    /**
     * Get option text
     *
     * @param int|string $value
     * @return null|string
     */
    public function getOptionText($value)
    {
        $options = $this->_getValues();
        if (isset($options[$value])) {
            return $options[$value];
        }
        return null;
    }

    /**
     * Get values
     *
     * @return array
     */
    protected function _getValues()
    {
        return [
            \Magento\GiftCard\Model\Giftcard::TYPE_VIRTUAL => __('Virtual'),
            \Magento\GiftCard\Model\Giftcard::TYPE_PHYSICAL => __('Physical'),
            \Magento\GiftCard\Model\Giftcard::TYPE_COMBINED => __('Combined')
        ];
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
                'unsigned' => true,
                'default' => null,
                'extra' => null,
                'type' => Table::TYPE_SMALLINT,
                'nullable' => true,
                'comment' => 'Enterprise Giftcard Type ' . $attributeCode . ' column',
            ],
        ];
    }

    /**
     * Retrieve select for flat attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     * @codeCoverageIgnore
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->_eavAttributeFactory->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
