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
namespace Magento\Adminhtml\Block\Customer\Grid\Filter;

class Country
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var \Magento\Directory\Model\Resource\Country\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $collectionFactory
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Directory\Model\Resource\Country\CollectionFactory $collectionFactory,
        \Magento\Backend\Block\Context $context,
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
