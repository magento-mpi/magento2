<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms page version collection
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Model_Mysql4_Version_Collection  extends Enterprise_Cms_Model_Mysql4_Collection_Abstract
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('enterprise_cms/version');
    }

    /**
     * Add access level filter.
     * Can take parameter array or one level.
     *
     * @param mixed $level
     * @return Enterprise_Cms_Model_Mysql4_Version_Collection
     */
    public function addAccessLevelFilter($level)
    {
        if (is_array($level)) {
            $this->addFieldToFilter('access_level', array('in' => $level));
        } else {
            $this->addFieldToFilter('access_level', $level);
        }

        return $this;
    }

    /**
     * Prepare two dimensional array basing on version_id as key and
     * version label as value data from collection.
     *
     * @return array
     */
    public function getIdLabelArray()
    {
        return $this->_toOptionHash('version_id', 'version_label');
    }

    /**
     * Join revision data by version id
     *
     * @return Enterprise_Cms_Model_Mysql4_Version_Collection
     */
    public function joinRevisions()
    {
        $this->getSelect()->joinLeft(
            array('rev_table' => $this->getTable('enterprise_cms/revision')),
            'rev_table.version_id=main_table.version_id', '*');
        return $this;
    }

    /**
     * Join version label or its number in case label is not defined
     *
     * @return Enterprise_Cms_Model_Mysql4_Revision_Collection
     */
    public function addVersionLabelToSelect()
    {
        $this->_map['fields']['version_label'] = 'IF(main_table.label = "", main_table.version_id, main_table.label )';
        $this->getSelect()->from('', array('version_label' => $this->_map['fields']['version_label']));

        return $this;
    }
}
