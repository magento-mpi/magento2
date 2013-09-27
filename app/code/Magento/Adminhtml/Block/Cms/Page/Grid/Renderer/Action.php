<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Cms\Page\Grid\Renderer;

class Action
    extends \Magento\Adminhtml\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Core\Model\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @param \Magento\Core\Model\UrlFactory $urlFactory
     * @param \Magento\Backend\Block\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\UrlFactory $urlFactory,
        \Magento\Backend\Block\Context $context,
        array $data = array()
    ) {
        $this->_urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    public function render(\Magento\Object $row)
    {
        /** @var \Magento\Core\Model\Url $urlModel */
        $urlModel = $this->_urlFactory->create()->setStore($row->getData('_first_store_id'));
        $href = $urlModel->getUrl(
            $row->getIdentifier(), array(
                '_current' => false,
                '_query' => '___store='.$row->getStoreCode()
           )
        );
        return '<a href="'.$href.'" target="_blank">'.__('Preview').'</a>';
    }
}
