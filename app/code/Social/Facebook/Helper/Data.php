<?php
/**
 * {license_notice}
 *
 * @category    Social
 * @package     Social_Facebook
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Facebook helper
 *
 * @category   Social
 * @package    Social_Facebook
 */
class Social_Facebook_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Checks whether Facebook module is enabled for frontend in system config
     *
     * @return bool
     */
    public function isEnabled()
    {
        return Mage::getStoreConfigFlag(Social_Facebook_Model_Facebook::XML_PATH_ENABLED);
    }

    /**
     * Get Facebook App Id
     *
     * @return string
     */
    public function getAppId()
    {
        return Mage::getStoreConfig(Social_Facebook_Model_Facebook::XML_PATH_APP_ID);
    }

     /**
     * Get Facebook App Secret
     *
     * @return string
     */
    public function getAppSecret()
    {
        return Mage::getStoreConfig(Social_Facebook_Model_Facebook::XML_PATH_APP_SECRET);
    }

     /**
     * Get Facebook App Name
     *
     * @return string
     */
    public function getAppName()
    {
        return Mage::getStoreConfig(Social_Facebook_Model_Facebook::XML_PATH_APP_NAME);
    }

     /**
     * Get Facebook App Name
     *
     * @return string
     */
    public function getObjectType()
    {
        return Mage::getStoreConfig(Social_Facebook_Model_Facebook::XML_PATH_APP_OBJECT_TYPE);
    }

     /**
     * Get Facebook App Name
     *
     * @return string
     */
    public function getAllActions()
    {
        $actions = Mage::getStoreConfig(Social_Facebook_Model_Facebook::XML_PATH_APP_ACTIONS);
        $actions = unserialize($actions);
        return $actions;
    }

     /**
     * Get Facebook App Friend Count in FriendBox
     *
     * @param string $action
     * @return string
     */
    public function getAppFriendCount($action)
    {
        $count = 0;
        $actions = $this->getAllActions();
        if (!empty($actions)) {
            foreach ($actions as $act) {
                if ($act['action'] == $action) {
                    $count = $act['count'];
                    break;
                }
            }
        }
        if (empty($count)) {
            $count = Social_Facebook_Model_Facebook::XML_PATH_APP_USER_COUNT;
        }
        return $count;
    }

    /**
     * Get Redirect Url fo Facebook Authorization
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    public function getRedirectUrl($product)
    {
        return Social_Facebook_Model_Api::URL_GRAPH_DIALOG_OAUTH
            . '?client_id=' . $this->getAppId()
            . '&redirect_uri=' . urlencode($product->getUrlModel()->getUrlInStore($product))
            . '&scope=publish_actions'
            . '&response_type=code';
    }
}
