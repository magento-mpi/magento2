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
 * Downloadable link model
 *
 * @method \Magento\Downloadable\Model\Resource\Link _getResource()
 * @method \Magento\Downloadable\Model\Resource\Link getResource()
 * @method int getProductId()
 * @method \Magento\Downloadable\Model\Link setProductId(int $value)
 * @method int getSortOrder()
 * @method \Magento\Downloadable\Model\Link setSortOrder(int $value)
 * @method int getNumberOfDownloads()
 * @method \Magento\Downloadable\Model\Link setNumberOfDownloads(int $value)
 * @method int getIsShareable()
 * @method \Magento\Downloadable\Model\Link setIsShareable(int $value)
 * @method string getLinkUrl()
 * @method \Magento\Downloadable\Model\Link setLinkUrl(string $value)
 * @method string getLinkFile()
 * @method \Magento\Downloadable\Model\Link setLinkFile(string $value)
 * @method string getLinkType()
 * @method \Magento\Downloadable\Model\Link setLinkType(string $value)
 * @method string getSampleUrl()
 * @method \Magento\Downloadable\Model\Link setSampleUrl(string $value)
 * @method string getSampleFile()
 * @method \Magento\Downloadable\Model\Link setSampleFile(string $value)
 * @method string getSampleType()
 * @method \Magento\Downloadable\Model\Link setSampleType(string $value)
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Downloadable\Model;

class Link extends \Magento\Core\Model\AbstractModel
{
    const XML_PATH_LINKS_TITLE              = 'catalog/downloadable/links_title';
    const XML_PATH_DEFAULT_DOWNLOADS_NUMBER = 'catalog/downloadable/downloads_number';
    const XML_PATH_TARGET_NEW_WINDOW        = 'catalog/downloadable/links_target_new_window';
    const XML_PATH_CONFIG_IS_SHAREABLE      = 'catalog/downloadable/shareable';

    const LINK_SHAREABLE_YES    = 1;
    const LINK_SHAREABLE_NO     = 0;
    const LINK_SHAREABLE_CONFIG = 2;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Downloadable\Model\Resource\Link');
        parent::_construct();
    }

    /**
     * Return link files path
     *
     * @return string
     */
    public static function getLinkDir()
    {
        return \Mage::getBaseDir();
    }

    /**
     * Enter description here...
     *
     * @return \Magento\Downloadable\Model\Link
     */
    protected function _afterSave()
    {
        $this->getResource()->saveItemTitleAndPrice($this);
        return parent::_afterSave();
    }

    /**
     * Retrieve base temporary path
     *
     * @return string
     */
    public static function getBaseTmpPath()
    {
        return \Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'links';
    }

    /**
     * Retrieve Base files path
     *
     * @return string
     */
    public static function getBasePath()
    {
        return \Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'files' . DS . 'links';
    }

    /**
     * Retrieve base sample temporary path
     *
     * @return string
     */
    public static function getBaseSampleTmpPath()
    {
        return \Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'tmp' . DS . 'link_samples';
    }

    /**
     * Retrieve base sample path
     *
     * @return string
     */
    public static function getBaseSamplePath()
    {
        return \Mage::getBaseDir('media') . DS . 'downloadable' . DS . 'files' . DS . 'link_samples';
    }

    /**
     * Retrieve links searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        return $this->_getResource()
            ->getSearchableData($productId, $storeId);
    }
}
