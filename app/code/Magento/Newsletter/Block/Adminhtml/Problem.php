<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter problem block template.
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Block\Adminhtml;

class Problem extends \Magento\Backend\Block\Template
{

    protected $_template = 'problem/list.phtml';

    /**
     * @var \Magento\Newsletter\Model\Resource\Problem\Collection
     */
    protected $_problemCollection;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Newsletter\Model\Resource\Problem\Collection $problemCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Newsletter\Model\Resource\Problem\Collection $problemCollection,
        array $data = array()
    ) {
        $this->_problemCollection = $problemCollection;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $collection = $this->_problemCollection->addSubscriberInfo()
            ->addQueueInfo();
    }

    protected function _prepareLayout()
    {
        $this->setChild('deleteButton',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button', 'del.button')
                ->setData(
                    array(
                        'label' => __('Delete Selected Problems'),
                        'onclick' => 'problemController.deleteSelected();'
                    )
                )
        );

        $this->setChild('unsubscribeButton',
            $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button', 'unsubscribe.button')
                ->setData(
                    array(
                        'label' => __('Unsubscribe Selected'),
                        'onclick' => 'problemController.unsubscribe();'
                    )
                )
        );
        return parent::_prepareLayout();
    }

    public function getUnsubscribeButtonHtml()
    {
        return $this->getChildHtml('unsubscribeButton');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('deleteButton');
    }

    public function getShowButtons()
    {
        return $this->_problemCollection->getSize() > 0;
    }
}
