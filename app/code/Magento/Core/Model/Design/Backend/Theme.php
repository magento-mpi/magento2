<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Design_Backend_Theme extends Magento_Core_Model_Config_Data
{
    /**
     * Design package instance
     *
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design = null;

    /**
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_design = $design;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Validate specified value against frontend area
     */
    protected function _beforeSave()
    {
        $design = clone $this->_design;
        $design->setDesignTheme($this->getValue(), Magento_Core_Model_App_Area::AREA_FRONTEND);
        return parent::_beforeSave();
    }
}
