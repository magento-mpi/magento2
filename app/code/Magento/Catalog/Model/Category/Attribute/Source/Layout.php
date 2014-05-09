<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Category\Attribute\Source;

/**
 * Catalog category landing page attribute source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
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
    public function __construct(\Magento\Theme\Model\Layout\Source\Layout $pageSourceLayout)
    {
        $this->_pageSourceLayout = $pageSourceLayout;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_pageSourceLayout->toOptionArray();
            array_unshift($this->_options, array('value' => '', 'label' => __('No layout updates')));
        }
        return $this->_options;
    }
}
