<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model\Entity\Attribute\Source;

class Boolean extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Option values
     */
    const VALUE_YES = 1;

    const VALUE_NO = 0;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * @var \Magento\Eav\Model\Resource\Entity\AttributeFactory
     */
    protected $_eavAttrEntity;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Eav\Model\Resource\Entity\AttributeFactory $eavAttrEntity
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Eav\Model\Resource\Entity\AttributeFactory $eavAttrEntity
    ) {
        $this->_coreData = $coreData;
        $this->_eavAttrEntity = $eavAttrEntity;
    }

    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array('label' => __('Yes'), 'value' => self::VALUE_YES),
                array('label' => __('No'), 'value' => self::VALUE_NO)
            );
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option['value']] = $option['label'];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|int $value
     * @return string|false
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return false;
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
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'length' => 1,
                'nullable' => true,
                'comment' => $attributeCode . ' column',
            ],
        ];
    }

    /**
     * Retrieve Indexes(s) for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = array();

        $index = 'IDX_' . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = array('type' => 'index', 'fields' => array($this->getAttribute()->getAttributeCode()));

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return \Magento\Framework\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return $this->_eavAttrEntity->create()->getFlatUpdateSelect($this->getAttribute(), $store);
    }

    /**
     * Get a text for index option value
     *
     * @param  string|int $value
     * @return string|bool
     */
    public function getIndexOptionText($value)
    {
        switch ($value) {
            case self::VALUE_YES:
                return 'Yes';
            case self::VALUE_NO:
                return 'No';
        }

        return parent::getIndexOptionText($value);
    }
}
