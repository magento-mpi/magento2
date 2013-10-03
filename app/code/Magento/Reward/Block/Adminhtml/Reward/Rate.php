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
 * Reward rate grid container
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Reward;

class Rate extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Block constructor
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reward';
        $this->_controller = 'adminhtml_reward_rate';
        $this->_headerText = __('Reward Exchange Rates');
        parent::_construct();
        $this->_updateButton('add', 'label', __('Add New Rate'));
    }
}
