<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sitemap\Block\Adminhtml;

/**
 * Sitemap edit form container
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Init container
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'sitemap_id';
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Magento_Sitemap';

        parent::_construct();

        $this->buttonList->add(
            'generate',
            array(
                'label' => __('Save & Generate'),
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array(
                            'event' => 'save',
                            'target' => '#edit_form',
                            'eventData' => array('action' => array('args' => array('generate' => '1')))
                        )
                    )
                ),
                'class' => 'add'
            )
        );
    }

    /**
     * Get edit form container header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('sitemap_sitemap')->getId()) {
            return __('Edit Sitemap');
        } else {
            return __('New Sitemap');
        }
    }
}
