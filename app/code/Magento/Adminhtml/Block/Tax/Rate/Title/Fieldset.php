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
 * Tax Rate Titles Fieldset
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Tax\Rate\Title;

class Fieldset extends \Magento\Data\Form\Element\Fieldset
{
    /**
     * @var \Magento\Adminhtml\Block\Tax\Rate\Title
     */
    protected $_title;

    /**
     * @param \Magento\Adminhtml\Block\Tax\Rate\Title $title
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Data\Form\Element\CollectionFactory $factoryCollection
     * @param array $attributes
     */
    public function __construct(
        \Magento\Adminhtml\Block\Tax\Rate\Title $title,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Data\Form\Element\Factory $factoryElement,
        \Magento\Data\Form\Element\CollectionFactory $factoryCollection,
        $attributes = array()
    ) {
        $this->_title = $title;
        parent::__construct($coreData, $factoryElement, $factoryCollection, $attributes);
    }

    public function getBasicChildrenHtml()
    {
        return $this->_title->toHtml();
    }
}
