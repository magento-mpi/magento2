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
class Magento_Adminhtml_Block_Newsletter_Problem extends Magento_Adminhtml_Block_Template
{

    protected $_template = 'newsletter/problem/list.phtml';

    /**
     * @var Magento_Newsletter_Model_Resource_Problem_Collection
     */
    protected $_problemCollection;

    /**
     * @param Magento_Newsletter_Model_Resource_Problem_Collection $problemCollection
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Newsletter_Model_Resource_Problem_Collection $problemCollection,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_problemCollection = $problemCollection;
        parent::__construct($coreData, $context, $data);
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
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button', 'del.button')
                ->setData(
                    array(
                        'label' => __('Delete Selected Problems'),
                        'onclick' => 'problemController.deleteSelected();'
                    )
                )
        );

        $this->setChild('unsubscribeButton',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button', 'unsubscribe.button')
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
        return  $this->_problemCollection->getSize() > 0;
    }
}// Class Magento_Adminhtml_Block_Newsletter_Problem END
