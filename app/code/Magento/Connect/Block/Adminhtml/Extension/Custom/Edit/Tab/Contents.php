<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for contents
 *
 * @category    Magento
 * @package     Magento_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab;

class Contents
    extends \Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab\AbstractTab
{
    /**
     * Extension factory
     *
     * @var \Magento\Connect\Model\ExtensionFactory
     */
    protected $_extensionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Connect\Model\Session $session
     * @param \Magento\Connect\Model\ExtensionFactory $extensionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Connect\Model\Session $session,
        \Magento\Connect\Model\ExtensionFactory $extensionFactory,
        array $data = array()
    ) {
        $this->_extensionFactory = $extensionFactory;
        parent::__construct($context, $registry, $formFactory, $session, $data);
    }

    /**
     * Retrieve list of targets
     *
     * @return array
     */
    public function getMageTargets()
    {
        $targets = $this->_extensionFactory->create()->getLabelTargets();
        if (!is_array($targets)) {
            $targets = array();
        }
        return $targets;
    }

    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Contents');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Contents');
    }
}
