<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product;

/**
 * @method setImageType(string)
 * @method string getImageType()
 * @method setImageWidth(string)
 * @method string getImageWidth()
 * @method setImageHeight(string)
 * @method string getImageHeight()
 * @method setImageLabel(string)
 * @method string getImageLabel()
 * @method setAddWhiteBorders(bool)
 * @method bool getAddWhiteBorders()
 * @method \Magento\Catalog\Helper\Image getImageHelper()
 * @method setImageHelper(\Magento\Catalog\Helper\Image $imageHelper)
 * @method \Magento\Catalog\Model\Product getProduct()
 *
 * Product image block
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Image extends \Magento\View\Block\Template
{
    /**
     * Template image only
     *
     * @var string
     */
    protected $_templateImage = 'Magento_Catalog::product/image.phtml';

    /**
     * Template image with html frame border
     *
     * @var string
     */
    protected $_templateWithBorders = 'Magento_Catalog::product/image_with_borders.phtml';

    /**
     * @var \Magento\Catalog\Model\Product\Image\View
     */
    protected $_productImageView;

    /**
     * @param \Magento\View\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Catalog\Model\Product\Image\View $productImageView
     * @param array $data
     */
    public function __construct(
        \Magento\View\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Catalog\Model\Product\Image\View $productImageView,
        array $data = array()
    ) {
        $this->_productImageView = $productImageView;
        parent::__construct($context, $coreData, $data);
    }

    /**
     * Initialize model
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $location
     * @param string $module
     * @return \Magento\Catalog\Block\Product\Image
     */
    public function init(\Magento\Catalog\Model\Product $product, $location, $module = 'Magento_Catalog')
    {
        $this->_productImageView->init($product, $location, $module);
        $this->_initTemplate();
        return $this;
    }

    /**
     * Select a template based on white_border flag
     *
     * @return \Magento\Catalog\Block\Product\Image
     */
    protected function _initTemplate()
    {
        if (null === $this->getTemplate()) {
            $template = $this->getProductImageView()->isWhiteBorders()
                ? $this->_templateImage
                : $this->_templateWithBorders;
            $this->setTemplate($template);
        }
        return $this;
    }

    /**
     * Getter for product image view model
     *
     * @return \Magento\Catalog\Model\Product\Image\View
     */
    public function getProductImageView()
    {
        return $this->_productImageView;
    }
}
