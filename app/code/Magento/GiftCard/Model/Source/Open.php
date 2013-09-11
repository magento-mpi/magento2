<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCard\Model\Source;

class Open extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $result = array();
        foreach ($this->_getValues() as $k => $v) {
            $result[] = array(
                'value' => $k,
                'label' => $v,
            );
        }

        return $result;
    }

    /**
     * Get option text
     *
     * @return string|null
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
            \Magento\GiftCard\Model\Giftcard::OPEN_AMOUNT_ENABLED  => __('Yes'),
        );
    }

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        $attributeDefaultValue = $this->getAttribute()->getDefaultValue();
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $attributeType = $this->getAttribute()->getBackendType();
        $isNullable = is_null($attributeDefaultValue) || empty($attributeDefaultValue);

        $column = array(
            'unsigned' => false,
            'extra'    => null,
            'default'  => $isNullable ? null : $attributeDefaultValue
        );

        if (\Mage::helper('Magento\Core\Helper\Data')->useDbCompatibleMode()) {
            $column['type']     = $attributeType;
            $column['is_null']  = $isNullable;
        } else {
            $column['type']     = \Mage::getResourceHelper('Magento_Eav')->getDdlTypeByColumnType($attributeType);
            $column['nullable'] = $isNullable;
            $column['comment']  = 'Enterprise Giftcard Open ' . $attributeCode . ' column';
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
        return \Mage::getResourceModel('Magento\Eav\Model\Resource\Entity\Attribute')
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}
