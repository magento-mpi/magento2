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
    extends \Magento\Adminhtml\Block\Template
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
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
     * @return boolean
     */
    public function canShowTab()
    {
        $customer = \Mage::registry('current_customer');
        return $customer->getId()
            && \Mage::helper('Magento\Reward\Helper\Data')->isEnabled()
            && $this->_authorization->isAllowed(\Magento\Reward\Helper\Data::XML_PATH_PERMISSION_BALANCE);
    }

    /**
     * Check if tab hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare layout.
     * Add accordion items
     *
     * @return \Magento\Reward\Block\Adminhtml\Customer\Edit\Tab\Reward
     */
    protected function _prepareLayout()
    {
        $accordion = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Accordion');
        $accordion->addItem('reward_points_history', array(
            'title'       => __('Reward Points History'),
            'open'        => false,
            'class'       => '',
            'ajax'        => true,
            'content_url' => $this->getUrl('*/customer_reward/history', array('_current' => true))
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
