<?php
/**
 * Saas queue catalog observer
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Remove css and js file from CDN
 *
 * @category    Saas
 * @package     Saas_Queue
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Cdn_Model_Observer_Cssjs extends Saas_Queue_Model_ObserverAbstract
{

    /**
     * Indexer of cache observer model
     *
     * @var Saas_Cdn_Model_CdnInterface
     */
    protected $_cdnAdapter;

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

   /**
     * Basic class initialization
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Saas_Cdn_Model_CdnInterface $adapter
     */
   public function __construct(
       Mage_Core_Model_Theme $theme,
       Saas_Cdn_Model_CdnInterface $adapter
   )
   {
       $this->_theme = $theme;
       $this->_cdnAdapter   = $adapter;
   }

    /**
     * {@inheritdoc}
     */
    public function useInEmailNotification()
    {
        return false;
    }

    /**
     * Reindex all processes
     *
     * @param  Varien_Event_Observer $observer
     * @return Saas_Queue_Model_Observer_Cssjs
     */
    public function processRefreshCssAndJs(Varien_Event_Observer $observer)
   {
       $this->_cdnAdapter->deleteRecursively($this->_theme->getBaseCustomizationsPath());

       return $this;
   }
}