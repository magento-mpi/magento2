<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Model\Source;

class Open extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
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
     * Resource helper
     *
     * @var \Magento\Eav\Model\Resource\Helper
     */
    protected $_resourceHelper;

    /**
     * @param \Magento\Eav\Model\Resource\Entity\AttributeFactory $eavAttributeFactory
     * @param \Magento\Eav\Model\Resource\Helper $resourceHelper
     * @param \Magento\Core\Helper\Data $coreData
     */
    public function __construct(
        \Magento\Eav\Model\Resource\Helper $resourceHelper,
        \Magento\Eav\Model\Resource\Entity\AttributeFactory $eavAttributeFactory,
        \Magento\Core\Helper\Data $coreData
    ) {
        $this->_resourceHelper = $resourceHelper;
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
        $result = array();
        foreach ($this->_getValues() as $k => $v) {
            $result[] = array('value' => $k, 'label' => $v);
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
        return array(
            \Magento\GiftCard\Model\Giftcard::OPEN_AMOUNT_DISABLED => __('No'),
            \Magento\GiftCard\Model\Giftcard::OPEN_AMOUNT_ENABLED => __('Yes')
        );
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColumns()
    {
        $attributeDefaultValue = $this->getAttribute()->getDefaultValue();
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeType = $this->getAttribute()->getBackendType();
        $isNullable = is_null($attributeDefaultValue) || empty($attributeDefaultValue);

        return [
            $attributeCode => [
                'unsigned' => false,
                'extra' => null,
                'default' => $isNullable ? null : $attributeDefaultValue,
                'type' => $this->_resourceHelper->getDdlTypeByColumnType($attributeType),
                'nullable' => $isNullable,
                'comment' => 'Enterprise Giftcard Open ' . $attributeCode . ' column',
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
