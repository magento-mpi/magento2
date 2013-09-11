<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method string|array getInputNames()
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Serializer extends Magento_Core_Block_Template
{

    /**
     * Set serializer template
     *
     * @return Magento_Backend_Block_Widget_Grid_Serializer
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Magento_Backend::widget/grid/serializer.phtml');
        return $this;
    }

    /**
     * Get grid column input names to serialize
     *
     * @param bool $asJSON
     *
     * @return string|array
     */
    public function getColumnInputNames($asJSON = false)
    {
        if ($asJSON) {
            return Mage::helper('Magento_Core_Helper_Data')->jsonEncode((array)$this->getInputNames());
        }
        return (array)$this->getInputNames();
    }

    /**
     * Get object data as JSON
     *
     * @return string
     */
    public function getDataAsJSON()
    {
        $result = array();
        $inputNames = $this->getInputNames();
        if ($serializeData = $this->getSerializeData()) {
            $result = $serializeData;
        } elseif (!empty($inputNames)) {
            return '{}';
        }
        return Mage::helper('Magento_Core_Helper_Data')->jsonEncode($result);
    }


    /**
     * Initialize grid block
     *
     * Get grid block from layout by specified block name
     * Get serialize data to manage it (called specified method, that return data to manage)
     * Also use reload param name for saving grid checked boxes states
     *
     *
     * @param Magento_Backend_Block_Widget_Grid | string $grid grid object or grid block name
     * @param string $callback block method  to retrieve data to serialize
     * @param string $hiddenInputName hidden input name where serialized data will be store
     * @param string $reloadParamName name of request parameter that will be used to save setted data while reload grid
     */
    public function initSerializerBlock($grid, $callback, $hiddenInputName, $reloadParamName = 'entityCollection')
    {
        if (is_string($grid)) {
            $grid = $this->getLayout()->getBlock($grid);
        }
        if ($grid instanceof Magento_Backend_Block_Widget_Grid) {
            $this->setGridBlock($grid)
                 ->setInputElementName($hiddenInputName)
                 ->setReloadParamName($reloadParamName)
                 ->setSerializeData($grid->$callback());
        }
    }

}
