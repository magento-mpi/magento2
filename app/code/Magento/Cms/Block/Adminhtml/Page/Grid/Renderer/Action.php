<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Block\Adminhtml\Page\Grid\Renderer;

class Action
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Core\Model\UrlFactory
     */
    protected $_urlFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Core\Model\UrlFactory $urlFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Core\Model\UrlFactory $urlFactory,
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
