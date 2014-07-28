<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Model;

class LocaleQuantityProcessor
{
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Zend_Filter_LocalizedToNormalized
     */
    protected $localFilter;

    /**
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->localeResolver = $localeResolver;
    }

    /**
     * Process localized quantity to internal format
     *
     * @param float $qty
     * @return array|string
     */
    public function process($qty)
    {
        if (!$this->localFilter) {
            $this->localFilter = new \Zend_Filter_LocalizedToNormalized(
                array('locale' => $this->localeResolver->getLocaleCode())
            );
        }
        $qty = $this->localFilter->filter((double)$qty);
        if ($qty < 0) {
            $qty = null;
        }
        return $qty;

    }
}
