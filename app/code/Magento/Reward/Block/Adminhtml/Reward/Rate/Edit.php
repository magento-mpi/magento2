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
 * Reward rate edit container
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Reward\Rate;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Internal constructor
     */
    protected function _construct()
    {
        $this->_objectId = 'rate_id';
        $this->_blockGroup = 'Magento_Reward';
        $this->_controller = 'adminhtml_reward_rate';
        parent::_construct();
    }

    /**
     * Getter.
     * Return header text in order to create or edit rate
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_reward_rate')->getId()) {
            return __('Edit Reward Exchange Rate');
        } else {
            return __('New Reward Exchange Rate');
        }
    }

    /**
     * rate validation URL getter
     *
     */
    public function getValidationUrl()
    {
        return $this->getUrl('adminhtml/*/validate', array('_current'=>true));
    }
}
