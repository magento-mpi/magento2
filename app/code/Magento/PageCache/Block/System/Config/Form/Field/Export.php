<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\PageCache\Block\System\Config\Form\Field;

/**
 * Class Export
 *
 * @package Magento\PageCache\Block\System\Config\Form\Field
 */
class Export extends \Magento\Backend\Block\System\Config\Form\Field
{
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

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $this->getForm()
            ->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );

        $url = $this->getUrl("*/PageCache/exportVarnishConfig", $params);
        $data = array(
            'id'        => 'export_varnish_configuration',
            'label'     => __('Export VCL'),
            'onclick'   => "setLocation('" . $url . ' )"'
        );

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}