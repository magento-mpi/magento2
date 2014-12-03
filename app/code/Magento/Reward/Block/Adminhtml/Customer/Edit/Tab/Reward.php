<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward tab block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Customer\Edit\Tab;

class Reward extends \Magento\Backend\Block\Template implements \Magento\Ui\Component\Layout\Tabs\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Reward data
     *
     * @var \Magento\Reward\Helper\Data
     */
    protected $_rewardData = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Reward\Helper\Data $rewardData
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        $this->_rewardData = $rewardData;
        parent::__construct($context, $data);
    }

    /**
     * Return tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Reward Points');
    }

    /**
     * Return tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Reward Points');
    }

    /**
     * Check if can show tab
     *
     * @return bool
     */
    public function canShowTab()
    {
        $customer = $this->_coreRegistry->registry('current_customer');
        return $customer->getId() && $this->_rewardData->isEnabled() && $this->_authorization->isAllowed(
            \Magento\Reward\Helper\Data::XML_PATH_PERMISSION_BALANCE
        );
    }

    /**
     * Check if tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab URL getter
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Prepare layout.
     * Add accordion items
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Accordion');
        $accordion->addItem(
            'reward_points_history',
            array(
                'title' => __('Reward Points History'),
                'open' => false,
                'class' => '',
                'ajax' => true,
                'content_url' => $this->getUrl('adminhtml/customer_reward/history', array('_current' => true))
            )
        );
        $this->setChild('history_accordion', $accordion);

        return parent::_prepareLayout();
    }

    /**
     * Precessor tab ID getter
     *
     * @return string
     */
    public function getAfter()
    {
        return 'reviews';
    }
}
