<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Block\Adminhtml\Review;

/**
 * Adminhtml report review product blocks content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Detail extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Reports';
        $this->_controller = 'adminhtml_review_detail';

        $product = $this->_productFactory->create()->load($this->getRequest()->getParam('id'));
        $this->_headerText = __('Reviews for %1', $product->getName());

        parent::_construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('reports/report_review/product/'));
        $this->_addBackButton();
    }
}
