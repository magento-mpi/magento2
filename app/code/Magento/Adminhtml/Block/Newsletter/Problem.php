<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter problem block template.
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Newsletter;

class Problem extends \Magento\Adminhtml\Block\Template
{

    protected $_template = 'newsletter/problem/list.phtml';


    protected function _construct()
    {
        parent::_construct();

        $collection = \Mage::getResourceSingleton('\Magento\Newsletter\Model\Resource\Problem\Collection')
            ->addSubscriberInfo()
            ->addQueueInfo();

    }

    protected function _prepareLayout()
    {
        $this->setChild('deleteButton',
            $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button','del.button')
                ->setData(
                    array(
                        'label' => __('Delete Selected Problems'),
                        'onclick' => 'problemController.deleteSelected();'
                    )
                )
        );

        $this->setChild('unsubscribeButton',
            $this->getLayout()->createBlock('\Magento\Adminhtml\Block\Widget\Button','unsubscribe.button')
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
        return  \Mage::getResourceSingleton('\Magento\Newsletter\Model\Resource\Problem\Collection')->getSize() > 0;
    }

}// Class \Magento\Adminhtml\Block\Newsletter\Problem END
