<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export CSV button for shipping table rates
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PageCache\Block\System\Config\Form\Field;

class Export extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $helper,
        array $data = array()
    ) {
        $this->_backendHelper = $helper;
        parent::__construct($context, $data);
    }


    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $this->getForm()
            ->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );

        $url = $this->_backendHelper->getUrl("*/*/exportVarnishConfig", $params);
        $data = array(
            'id'        => 'export_varnish_configuration',
            'label'     => __('Export VCL'),
            'onclick'   => "setLocation('" . $url . "varnish_configuration.vcl' )"
        );

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}