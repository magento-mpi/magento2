<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Export CSV button for shipping table rates
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\System\Config\Form\Field;

class Export extends \Magento\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\App\Helper\HelperFactory
     */
    protected $_helperFactory;

    /**
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Escaper $escaper
     * @param \Magento\App\Helper\HelperFactory $helperFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Escaper $escaper,
        \Magento\App\Helper\HelperFactory $helperFactory,
        array $data = array()
    ) {
        $this->_helperFactory = $helperFactory;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    public function getElementHtml()
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock  */
        $buttonBlock = $this->getForm()
            ->getParent()
            ->getLayout()
            ->createBlock('Magento\Backend\Block\Widget\Button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );

        $url = $this->_helperFactory->get('Magento\Backend\Helper\Data')->getUrl("*/*/exportTablerates", $params);
        $data = array(
            'label'     =>  __('Export CSV'),
            'onclick'   => "setLocation('" . $url
                . "conditionName/' + $('carriers_tablerate_condition_name').value + '/tablerates.csv' )",
            'class'     => '',
        );

        $html = $buttonBlock->setData($data)->toHtml();
        return $html;
    }
}
