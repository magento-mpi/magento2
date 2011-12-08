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
 * Enter description here ...
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Resource_Adapter_Item_Config
    extends Enterprise_Staging_Model_Resource_Adapter_Item_Default
{
    /**
     * Prepare simple select by given parameters
     *
     * @param mixed $table
     * @param string $fields
     * @param array | string $where
     * @return string
     */
    protected function _getSimpleSelect($table, $fields, $where = null)
    {
        $_where = array();
        if (!is_null($where)) {
            if (is_array($where)) {
                $_where = $where;
            } else {
                $_where[] = $where;
            }
        }

        $likeOptions = array('position' => 'any');
        if ($this->getEvent()->getCode() !== 'rollback') {
            $itemXmlConfig = $this->getConfig();
            if ($itemXmlConfig->ignore_nodes) {
                foreach ($itemXmlConfig->ignore_nodes->children() as $node) {
                    $path = (string) $node->path;
                    /* $helper Mage_Core_Model_Resource_Helper_Abstract */
                    $helper = Mage::getResourceHelper('Mage_Core');
                    $_where[] = 'path NOT LIKE ' . $helper->addLikeEscape($path, $likeOptions);
                }
            }
        }

        $select = parent::_getSimpleSelect($table, $fields, $_where);

        return $select;
    }
}
