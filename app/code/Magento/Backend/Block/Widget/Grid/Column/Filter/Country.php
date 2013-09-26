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
 * Country grid filter
 */
class Magento_Backend_Block_Widget_Grid_Column_Filter_Country
    extends Magento_Backend_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * @var Magento_Directory_Model_Resource_Country_CollectionFactory
     */
    protected $_directoriesFactory;

    /**
     * @param Magento_Backend_Block_Context $context
     * @param Magento_Core_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $directoriesFactory
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Context $context,
        Magento_Core_Model_Resource_Helper_Mysql4 $resourceHelper,
        Magento_Directory_Model_Resource_Country_CollectionFactory $directoriesFactory,
        array $data = array()
    ) {
        $this->_directoriesFactory = $directoriesFactory;
        parent::__construct($context, $resourceHelper, $data);
    }

    protected function _getOptions()
    {
        $options = $this->_directoriesFactory->create()->load()->toOptionArray(false);
        array_unshift($options, array('value' => '', 'label' => __('All Countries')));
        return $options;
    }
}
