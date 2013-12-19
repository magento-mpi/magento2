<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * System configuration shipping methods allow all countries selec
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\System\Config\Form\Field\Select;

class Flatcatalog
    extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @var \Magento\Catalog\Helper\Category\Flat
     */
    protected $_flatCategory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Helper\Category\Flat $flatCategory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Helper\Category\Flat $flatCategory,
        array $data = array()
    ) {
        $this->_flatCategory = $flatCategory;
        parent::__construct($context, $data);
    }

    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        if (!$this->_flatCategory->isBuilt()) {
            $element->setDisabled(true)
                ->setValue(0);
        }
        return parent::_getElementHtml($element);
    }

}
