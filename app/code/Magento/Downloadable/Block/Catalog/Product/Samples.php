<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Block\Catalog\Product;

use Magento\Downloadable\Model\Resource\Sample;

/**
 * Downloadable Product Samples part block
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Samples extends \Magento\Catalog\Block\Product\AbstractProduct
{
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

    /**
     * @param Sample $sample
     * @return string
     */
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
