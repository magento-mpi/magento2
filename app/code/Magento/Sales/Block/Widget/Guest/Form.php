<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales widget search form for orders and returns block
 */
namespace Magento\Sales\Block\Widget\Guest;

class Form
    extends \Magento\View\Element\Template
    implements \Magento\Widget\Block\BlockInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Check whether module is available
     *
     * @return bool
     */
    public function isEnable()
    {
        return !($this->_customerSession->isLoggedIn());
    }

    /**
     * Select element for choosing registry type
     *
     * @return array
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento\View\Element\Html\Select')
            ->setData(array(
                'id'    => 'quick_search_type_id',
                'class' => 'select guest-select',
            ))
            ->setName('oar_type')
            ->setOptions($this->_getFormOptions())
            ->setExtraParams('onchange="showIdentifyBlock(this.value);"');
        return $select->getHtml();
    }

    /**
     * Get Form Options for Guest
     *
     * @return array
     */
    protected function _getFormOptions()
    {
        $options = $this->getData('identifymeby_options');
        if (is_null($options)) {
            $options = array();
            $options[] = array(
                'value' => 'email',
                'label' => 'Email Address'
            );
            $options[] = array(
                'value' => 'zip',
                'label' => 'ZIP Code'
            );
            $this->setData('identifymeby_options', $options);
        }

        return $options;
    }

    /**
     * Return quick search form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('sales/guest/view');
    }
}
