<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Renderer for service JavaScript code that disables corresponding paypal methods on page load
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Block\Adminhtml\System\Config\Fieldset;

class Store
    extends \Magento\Backend\Block\Template
    implements \Magento\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Path to template file
     *
     * @var string
     */
    protected $_template = 'Magento_Paypal::system/config/fieldset/store.phtml';

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_coreConfig;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Config $coreConfig
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Config $coreConfig,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct(
            $coreData,
            $context,
            $data
        );
        $this->_coreConfig = $coreConfig;
        $this->_storeManager = $storeManager;
    }

    /**
     * Render service JavaScript code
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $html = $this->_storeManager->isSingleStoreMode() ? '' : $this->toHtml();
        return $html;
    }

    /**
     * Returns list of disabled (in the Default or the Website Scope) paypal methods
     *
     * @return array
     */
    protected function getPaypalDisabledMethods()
    {
        // Assoc array that contains info about paypal methods (their IDs and corresponding Config Paths)
        $methods = array(
            'express'   => 'payment/paypal_express/active',
            'wps'       => 'payment/paypal_standard/active',
            'wpp'       => 'payment/paypal_direct/active',
            'wpppe'     => 'payment/paypaluk_direct/active',
            'verisign'  => 'payment/verisign/active',
            'expresspe' => 'payment/paypaluk_express/active'
        );
        // Retrieve a code of the current website
        $website = $this->getRequest()->getParam('website');
        $disabledMethods = array();
        foreach ($methods as $methodId => $methodPath) {
            $isEnabled = (int)$this->_coreConfig->getValue($methodPath, 'website', $website);
            if ($isEnabled === 0) {
                $disabledMethods[$methodId] = $isEnabled;
            }
        }
        return $disabledMethods;
    }
}
