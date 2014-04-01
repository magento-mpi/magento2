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
 * Widget to display link to the category
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Category\Widget;

class Link extends \Magento\Catalog\Block\Widget\Link
{
    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Resource\Url\Rewrite $urlRewrite
     * @param \Magento\Catalog\Model\Resource\Category $resourceCategory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Resource\Url\Rewrite $urlRewrite,
        \Magento\Catalog\Model\Resource\Category $resourceCategory,
        array $data = array()
    ) {
        parent::__construct($context, $urlRewrite, $data);
        $this->_entityResource = $resourceCategory;
    }
}
