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
 * Bundle Price View Attribute Renderer
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Model\Product\Attribute\Source\Price;

class View extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'label' => __('As Low as'),
                    'value' =>  1
                ),
                array(
                    'label' => __('Price Range'),
                    'value' =>  0
                ),
            );
        }
        return $this->_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
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
    public function getFlatColums()
    {
        $attributeCode = $this->getAttribute()->getAttributeCode();
        $column = array(
            'unsigned'  => false,
            'default'   => null,
            'extra'     => null
        );

        if (\Mage::helper('Magento\Core\Helper\Data')->useDbCompatibleMode()) {
            $column['type']     = 'int';
            $column['is_null']  = true;
        } else {
            $column['type']     = \Magento\DB\Ddl\Table::TYPE_INTEGER;
            $column['nullable'] = true;
            $column['comment']  = 'Bundle Price View ' . $attributeCode . ' column';
        }

        return array($attributeCode => $column);
   }

    /**
     * Retrieve Select for update Attribute value in flat table
     *
     * @param   int $store
     * @return  \Magento\DB\Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return \Mage::getResourceModel('\Magento\Eav\Model\Resource\Entity\Attribute\Option')
            ->getFlatUpdateSelect($this->getAttribute(), $store, false);
    }
}
