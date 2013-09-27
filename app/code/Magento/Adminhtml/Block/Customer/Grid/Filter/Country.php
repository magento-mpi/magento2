<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Country customer grid column filter
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_Grid_Filter_Country
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    /**
     * @var Magento_Directory_Model_Resource_Country_CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param Magento_Directory_Model_Resource_Country_CollectionFactory $collectionFactory
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Directory_Model_Resource_Country_CollectionFactory $collectionFactory,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
    }

    protected function _getOptions()
    {
        $options = $this->_collectionFactory->load()->toOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>__('All countries')));
        return $options;
    }
}
