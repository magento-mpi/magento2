<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tax Rate Titles Fieldset
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Block\Adminhtml\Rate\Title;

class Fieldset extends \Magento\Data\Form\Element\Fieldset
{
    /**
     * @var \Magento\Tax\Block\Adminhtml\Rate\Title
     */
    protected $_title;

    /**
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Escaper $escaper
     * @param \Magento\Tax\Block\Adminhtml\Rate\Title $title
     * @param array $data
     */
    public function __construct(
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Escaper $escaper,
        \Magento\Tax\Block\Adminhtml\Rate\Title $title,
        $data = array()
    ) {
        $this->_title = $title;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @return string
     */
    public function getBasicChildrenHtml()
    {
        return $this->_title->toHtml();
    }
}
