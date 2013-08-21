<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tag Frontend controller
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Controller_Index extends Magento_Core_Controller_Front_Action
{
    /**
     * Saving tag and relation between tag, customer, product and store
     */
    public function saveAction()
    {
        /** @var $customerSession Magento_Customer_Model_Session */
        $customerSession = Mage::getSingleton('Magento_Customer_Model_Session');
        if (!$customerSession->authenticate($this)) {
            return;
        }
        $tagName    = (string) $this->getRequest()->getQuery('productTagName');
        $productId  = (int)$this->getRequest()->getParam('product');

        if (strlen($tagName) && $productId) {
            /** @var $session Magento_Core_Model_Session_Generic */
            $session = Mage::getSingleton('Magento_Tag_Model_Session');
            $product = Mage::getModel('Magento_Catalog_Model_Product')
                ->load($productId);
            if (!$product->getId()) {
                $session->addError(__('We couldn\'t save the tag(s).'));
            } else {
                try {
                    $customerId = $customerSession->getCustomerId();
                    $storeId = Mage::app()->getStore()->getId();

                    /** @var $tagModel Magento_Tag_Model_Tag */
                    $tagModel = Mage::getModel('Magento_Tag_Model_Tag');

                    // added tag relation statuses
                    $counter = array(
                        Magento_Tag_Model_Tag::ADD_STATUS_NEW => array(),
                        Magento_Tag_Model_Tag::ADD_STATUS_EXIST => array(),
                        Magento_Tag_Model_Tag::ADD_STATUS_SUCCESS => array(),
                        Magento_Tag_Model_Tag::ADD_STATUS_REJECTED => array()
                    );

                    $tagNamesArr = $this->_cleanTags($this->_extractTags($tagName));
                    foreach ($tagNamesArr as $tagName) {
                        // unset previously added tag data
                        $tagModel->unsetData();
                        $tagModel->loadByName($tagName);

                        if (!$tagModel->getId()) {
                            $tagModel->setName($tagName)
                                ->setFirstCustomerId($customerId)
                                ->setFirstStoreId($storeId)
                                ->setStatus($tagModel->getPendingStatus())
                                ->save();
                        }
                        $relationStatus = $tagModel->saveRelation($productId, $customerId, $storeId);
                        $counter[$relationStatus][] = $tagName;
                    }
                    $this->_fillMessageBox($counter);
                } catch (Exception $e) {
                    Mage::logException($e);
                    $session->addError(__('We couldn\'t save the tag(s).'));
                }
            }
        }
        $this->_redirectReferer();
    }

    /**
     * Checks inputed tags on the correctness of symbols and split string to array of tags
     *
     * @param string $tagNamesInString
     * @return array
     */
    protected function _extractTags($tagNamesInString)
    {
        return explode("\n", preg_replace("/(\'(.*?)\')|(\s+)/i", "$1\n", $tagNamesInString));
    }

    /**
     * Clears the tag from the separating characters.
     *
     * @param array $tagNamesArr
     * @return array
     */
    protected function _cleanTags(array $tagNamesArr)
    {
        foreach ($tagNamesArr as $key => $tagName) {
            $tagNamesArr[$key] = trim($tagNamesArr[$key], '\'');
            $tagNamesArr[$key] = trim($tagNamesArr[$key]);
            if ($tagNamesArr[$key] == '') {
                unset($tagNamesArr[$key]);
            }
        }
        return $tagNamesArr;
    }

    /**
     * Fill Message Box by success and notice messages about results of user actions.
     *
     * @param array $counter
     * @return void
     */
    protected function _fillMessageBox($counter)
    {
        /** @var $session Magento_Core_Model_Session_Generic */
        $session = Mage::getSingleton('Magento_Tag_Model_Session');
        $helper = Mage::helper('Magento_Core_Helper_Data');

        if (count($counter[Magento_Tag_Model_Tag::ADD_STATUS_NEW])) {
            $tagsCount = count($counter[Magento_Tag_Model_Tag::ADD_STATUS_NEW]);
            $session->addSuccess(__('%1 tag(s) have been accepted for moderation.', $tagsCount));
        }

        if (count($counter[Magento_Tag_Model_Tag::ADD_STATUS_EXIST])) {
            foreach ($counter[Magento_Tag_Model_Tag::ADD_STATUS_EXIST] as $tagName) {
                $session->addNotice(
                    __('Tag "%1" has already been added to the product.', $helper->escapeHtml($tagName))
                );
            }
        }

        if (count($counter[Magento_Tag_Model_Tag::ADD_STATUS_SUCCESS])) {
            foreach ($counter[Magento_Tag_Model_Tag::ADD_STATUS_SUCCESS] as $tagName) {
                $session->addSuccess(
                    __('Tag "%1" has been added to the product.', $helper->escapeHtml($tagName))
                );
            }
        }

        if (count($counter[Magento_Tag_Model_Tag::ADD_STATUS_REJECTED])) {
            foreach ($counter[Magento_Tag_Model_Tag::ADD_STATUS_REJECTED] as $tagName) {
                $session->addNotice(
                    __('Tag "%1" has been rejected by the administrator.', $helper->escapeHtml($tagName))
                );
            }
        }
    }
}
