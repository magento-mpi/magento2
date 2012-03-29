<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging History Item View
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Log_View_Information_Rollback
    extends Enterprise_Staging_Block_Adminhtml_Log_View_Information_Default
{
    protected $_websites;

    /**
     * Retrieve target website on which backup was rollbacked
     * Returns array bc in map there is array of websites so there will no
     * problems in some cases in map will be several websites
     *
     * @return array
     */
    public function getTargetWebsites()
    {
        $_websites = array();
        foreach ($this->_mapper->getWebsites() as $stagingWebsiteId => $masterWebsite) {
            foreach ($masterWebsite as $id) {
                $_website = Mage::app()->getWebsite($id);
                if ($_website) {
                    $_websites[] = $_website;
                }
            }
        }
        return $_websites;
    }
}
