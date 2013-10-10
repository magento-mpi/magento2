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
 * Catalog category landing page attribute source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Category\Attribute\Source;

class Layout extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Page source layout
     *
     * @var \Magento\Page\Model\Source\Layout
     */
    protected $_pageSourceLayout;

    /**
     * Construct
     *
     * @param \Magento\Page\Model\Source\Layout $pageSourceLayout
     */
    public function __construct(
        \Magento\Page\Model\Source\Layout $pageSourceLayout
    ) {
        $this->_pageSourceLayout = $pageSourceLayout;
    }

    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_pageSourceLayout->toOptionArray();
            array_unshift($this->_options, array('value'=>'', 'label'=>__('No layout updates')));
        }
        return $this->_options;
    }
}
