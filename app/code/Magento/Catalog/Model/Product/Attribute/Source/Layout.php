<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Attribute\Source;

/**
 * Catalog product landing page attribute source
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Layout extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Core\Model\PageLayout\Config\Builder
     */
    protected $pageLayoutBuilder;

    /**
     * @param \Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder
     */
    public function __construct(\Magento\Core\Model\PageLayout\Config\Builder $pageLayoutBuilder)
    {
        $this->pageLayoutBuilder = $pageLayoutBuilder;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->pageLayoutBuilder->getPageLayoutsConfig()->toOptionArray();
            array_unshift($this->_options, array('value' => '', 'label' => __('No layout updates')));
        }
        return $this->_options;
    }
}
