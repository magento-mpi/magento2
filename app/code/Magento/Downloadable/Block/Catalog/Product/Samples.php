<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Downloadable Product Samples part block
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Block\Catalog\Product;

class Samples extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Construct
     *
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Registry $registry,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        parent::__construct($storeManager, $catalogConfig, $registry, $taxData, $catalogData, $coreData,
            $context, $data);
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function hasSamples()
    {
        return $this->getProduct()->getTypeInstance()
            ->hasSamples($this->getProduct());
    }

    /**
     * Get downloadable product samples
     *
     * @return array
     */
    public function getSamples()
    {
        return $this->getProduct()->getTypeInstance()
            ->getSamples($this->getProduct());
    }

    public function getSampleUrl($sample)
    {
        return $this->getUrl('downloadable/download/sample', array('sample_id' => $sample->getId()));
    }

    /**
     * Return title of samples section
     *
     * @return string
     */
    public function getSamplesTitle()
    {
        if ($this->getProduct()->getSamplesTitle()) {
            return $this->getProduct()->getSamplesTitle();
        }
        return $this->_storeConfig->getConfig(\Magento\Downloadable\Model\Sample::XML_PATH_SAMPLES_TITLE);
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return $this->_storeConfig->getConfigFlag(\Magento\Downloadable\Model\Link::XML_PATH_TARGET_NEW_WINDOW);
    }
}
