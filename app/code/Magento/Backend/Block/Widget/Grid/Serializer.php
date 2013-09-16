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
 *
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Block_Widget_Grid_Serializer extends Magento_Core_Block_Template
{

    /**
     * Store grid input names to serialize
     *
     * @var array
     */
    private $_inputsToSerialize = array();

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
     * Register grid column input name to serialize
     *
     * @param string $name
     */
    public function addColumnInputName($names)
    {
        if (is_array($names)) {
            foreach ($names as $name) {
                $this->addColumnInputName($name);
            }
        } else {
            if (!in_array($names, $this->_inputsToSerialize)) {
                $this->_inputsToSerialize[] = $names;
            }
        }
    }

    /**
     * Get grid column input names to serialize
     *
     * @return unknown
     */
    public function getColumnInputNames($asJSON = false)
    {
        if ($asJSON) {
            return $this->_coreData->jsonEncode($this->_inputsToSerialize);
        }
        return $this->_inputsToSerialize;
    }

    /**
     * Get object data as JSON
     *
     * @return string
     */
    public function getDataAsJSON()
    {
        $result = array();
        if ($serializeData = $this->getSerializeData()) {
            $result = $serializeData;
        } elseif (!empty($this->_inputsToSerialize)) {
            return '{}';
        }
        return $this->_coreData->jsonEncode($result);
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
