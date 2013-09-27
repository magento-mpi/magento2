<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Cms_Page_Grid_Renderer_Action
    extends Magento_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var Magento_Core_Model_UrlFactory
     */
    protected $_urlFactory;

    /**
     * @param Magento_Core_Model_UrlFactory $urlFactory
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_UrlFactory $urlFactory,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_urlFactory = $urlFactory;
        parent::__construct($context, $data);
    }

    public function render(Magento_Object $row)
    {
        /** @var Magento_Core_Model_Url $urlModel */
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
