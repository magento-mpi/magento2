<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Entity/Attribute/Model - attribute selection source from configuration
 *
 * this class should be abstract, but kept usual for legacy purposes
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Entity_Attribute_Source_Config extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Config Node Path
     *
     * @var Magento_Core_Model_Config_Element
     */
    protected $_configNodePath;

    /**
     * Retrieve all options for the source from configuration
     *
     * @throws Magento_Eav_Exception
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = array();
            $rootNode = null;
            if ($this->_configNodePath) {
                $rootNode = Mage::getConfig()->getNode($this->_configNodePath);
            }
            if (!$rootNode) {
                throw Mage::exception('Magento_Eav', __('Failed to load node %1 from config', $this->_configNodePath));
            }
            $options = $rootNode->children();
            if (empty($options)) {
                throw Mage::exception('Magento_Eav', __('No options found in config node %1', $this->_configNodePath));
            }
            foreach ($options as $option) {
                $this->_options[] = array(
                    'value' => (string)$option->value,
                    'label' => __((string)$option->label)
                );
            }
        }

        return $this->_options;
    }
}
