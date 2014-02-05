<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward tab block
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Customer\Edit\Tab;

class Reward
    extends \Magento\Backend\Block\Template
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
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
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Reward\Helper\Data $rewardData,
        \Magento\Core\Model\Registry $registry,
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
        return $customer->getId()
            && $this->_rewardData->isEnabled()
            && $this->_authorization->isAllowed(\Magento\Reward\Helper\Data::XML_PATH_PERMISSION_BALANCE);
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
     * Prepare layout.
     * Add accordion items
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Accordion');
        $accordion->addItem('reward_points_history', array(
            'title'       => __('Reward Points History'),
            'open'        => false,
            'class'       => '',
            'ajax'        => true,
            'content_url' => $this->getUrl('adminhtml/customer_reward/history', array('_current' => true))
        ));
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
