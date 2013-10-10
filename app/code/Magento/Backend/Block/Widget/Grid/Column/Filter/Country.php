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
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

class Country
    extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var \Magento\Directory\Model\Resource\Country\CollectionFactory
     */
    protected $_directoriesFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\Resource\Helper $resourceHelper
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $directoriesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\Resource\Helper $resourceHelper,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $directoriesFactory,
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
