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
 * Catalog category landing page attribute source
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Category_Attribute_Source_Layout
    extends Magento_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Return cms layout update options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $layouts = array();
            foreach ($this->_coreConfig->getNode('global/cms/layouts')->children() as $layoutName=>$layoutConfig) {
                $this->_options[] = array(
                   'value'=>$layoutName,
                   'label'=>(string)$layoutConfig->label
                );
            }
            array_unshift($this->_options, array('value'=>'', 'label' => __('No layout updates')));
        }
        return $this->_options;
    }
}
