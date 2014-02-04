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
 */
class Export extends \Magento\Backend\Block\System\Config\Form\Field
{
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
            'id'        => 'system_varnish_configuration_settings_varnish_configuration',
            'label'     => __('Export VCL'),
            'onclick'   => "setLocation('" . $url . "')"
        );

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}