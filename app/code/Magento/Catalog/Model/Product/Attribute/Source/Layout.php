<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute\Source;

/**
 * Catalog product landing page attribute source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Layout extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Page source layout
     *
     * @var \Magento\Theme\Model\Layout\Source\Layout
     */
    protected $_pageSourceLayout;

    /**
     * Construct
     *
     * @param \Magento\Theme\Model\Layout\Source\Layout $pageSourceLayout
     */
    public function __construct(
        \Magento\Theme\Model\Layout\Source\Layout $pageSourceLayout
    ) {
        $this->_pageSourceLayout = $pageSourceLayout;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_pageSourceLayout->toOptionArray();
            array_unshift($this->_options, array('value'=>'', 'label'=>__('No layout updates')));
        }
        return $this->_options;
    }
}
