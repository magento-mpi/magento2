<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Page Model
 *
 * @method Magento_Cms_Model_Resource_Page _getResource()
 * @method Magento_Cms_Model_Resource_Page getResource()
 * @method string getTitle()
 * @method Magento_Cms_Model_Page setTitle(string $value)
 * @method string getRootTemplate()
 * @method Magento_Cms_Model_Page setRootTemplate(string $value)
 * @method string getMetaKeywords()
 * @method Magento_Cms_Model_Page setMetaKeywords(string $value)
 * @method string getMetaDescription()
 * @method Magento_Cms_Model_Page setMetaDescription(string $value)
 * @method string getIdentifier()
 * @method Magento_Cms_Model_Page setIdentifier(string $value)
 * @method string getContentHeading()
 * @method Magento_Cms_Model_Page setContentHeading(string $value)
 * @method string getContent()
 * @method Magento_Cms_Model_Page setContent(string $value)
 * @method string getCreationTime()
 * @method Magento_Cms_Model_Page setCreationTime(string $value)
 * @method string getUpdateTime()
 * @method Magento_Cms_Model_Page setUpdateTime(string $value)
 * @method int getIsActive()
 * @method Magento_Cms_Model_Page setIsActive(int $value)
 * @method int getSortOrder()
 * @method Magento_Cms_Model_Page setSortOrder(int $value)
 * @method string getLayoutUpdateXml()
 * @method Magento_Cms_Model_Page setLayoutUpdateXml(string $value)
 * @method string getCustomTheme()
 * @method Magento_Cms_Model_Page setCustomTheme(string $value)
 * @method string getCustomRootTemplate()
 * @method Magento_Cms_Model_Page setCustomRootTemplate(string $value)
 * @method string getCustomLayoutUpdateXml()
 * @method Magento_Cms_Model_Page setCustomLayoutUpdateXml(string $value)
 * @method string getCustomThemeFrom()
 * @method Magento_Cms_Model_Page setCustomThemeFrom(string $value)
 * @method string getCustomThemeTo()
 * @method Magento_Cms_Model_Page setCustomThemeTo(string $value)
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Model_Page extends Magento_Core_Model_Abstract
{
    const NOROUTE_PAGE_ID = 'no-route';

    /**
     * Page's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    const CACHE_TAG              = 'cms_page';
    protected $_cacheTag         = 'cms_page';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'cms_page';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Cms_Model_Resource_Page');
    }

    /**
     * Load object data
     *
     * @param mixed $id
     * @param string $field
     * @return Magento_Cms_Model_Page
     */
    public function load($id, $field=null)
    {
        if (is_null($id)) {
            return $this->noRoutePage();
        }
        return parent::load($id, $field);
    }

    /**
     * Load No-Route Page
     *
     * @return Magento_Cms_Model_Page
     */
    public function noRoutePage()
    {
        return $this->load(self::NOROUTE_PAGE_ID, $this->getIdFieldName());
    }

    /**
     * Check if page identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Prepare page's statuses.
     * Available event cms_page_get_available_statuses to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return array(
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled'),
        );
    }
}
