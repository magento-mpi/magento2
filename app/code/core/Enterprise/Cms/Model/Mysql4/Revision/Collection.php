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
 * Cms page revision collection
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Model_Mysql4_Revision_Collection extends Enterprise_Cms_Model_Mysql4_Collection_Abstract
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('enterprise_cms/revision');
    }

    /**
     * Joining version data to reeach revision.
     * Columns which should be joined determined by parameter $cols.
     *
     * @param mixed $cols
     * @return Enterprise_Cms_Model_Mysql4_Revision_Collection
     */
    public function joinVersions($cols = Zend_Db_Select::SQL_WILDCARD)
    {
        $this->_map['fields']['version_id'] = 'ver_table.version_id';

        $this->getSelect()->joinInner(array('ver_table' => $this->getTable('enterprise_cms/version')),
            'ver_table.version_id = main_table.version_id', $cols);

        return $this;
    }
}
