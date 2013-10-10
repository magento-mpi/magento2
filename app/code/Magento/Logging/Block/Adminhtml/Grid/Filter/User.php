<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * User column filter for Event Log grid
 */
namespace Magento\Logging\Block\Adminhtml\Grid\Filter;

class User extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var \Magento\Logging\Model\Resource\EventFactory
     */
    protected $eventFactory;

    /**
     * @param \Magento\Logging\Model\Resource\EventFactory $eventFactory
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\Resource\Helper $resourceHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Logging\Model\Resource\EventFactory $eventFactory,
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\Resource\Helper $resourceHelper,
        array $data = array()
    ) {
        $this->eventFactory = $eventFactory;
        parent::__construct($context, $resourceHelper, $data);
    }

    /**
     * Build filter options list
     *
     * @return array
     */
    public function _getOptions()
    {
        $options = array(array('value' => '', 'label' => __('All Users')));
        foreach ($this->eventFactory->create()->getUserNames() as $username) {
            $options[] = array('value' => $username, 'label' => $username);
        }
        return $options;
    }

    /**
     * Filter condition getter
     *
     * @string
     */
    public function getCondition()
    {
        return $this->getValue();
    }
}
