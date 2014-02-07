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
     * @var \Magento\App\ConfigInterface
     */
    protected $config;

    public function __construct(
        \Magento\App\ConfigInterface $config,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ){
        $this->config = $config;
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
            'id'        => 'system_full_page_cache_varnish_export_button',
            'label'     => __('Export VCL'),
            'onclick'   => "setLocation('" . $url . "')"
        );

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }

    /**
     * Return PageCache TTL value from config
     * to avoid saving empty field
     *
     * @return string
     */
    public function getTtlValue()
    {
        return $this->config->getValue(\Magento\PageCache\Model\Config::XML_PAGECACHE_TTL);
    }
}