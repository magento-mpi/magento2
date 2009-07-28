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
 * Cms page version resource model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Model_Mysql4_Page_Version extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/page_version', 'version_id');
    }

    /**
     * Checking some moments before we can actually delete version
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Enterprise_Cms_Model_Mysql4_Page_Version
     */
    protected function _beforeDelete(Mage_Core_Model_Abstract $object)
    {
        /*
         * Checking if version id not last public for its page
         */
        $select = $this->_getReadAdapter()->select();
        $select->from($this->getMainTable(), 'count(*)')
            ->where('page_id = ?', $object->getPageId())
            ->where('access_level = ?', Enterprise_Cms_Model_Page_Version::ACCESS_LEVEL_PUBLIC)
            ->where('version_id <> ? ', $object->getVersionId());

        $result = $this->_getReadAdapter()->fetchOne($select);

        if (!$result) {
            Mage::throwException(
                Mage::helper('enterprise_cms')->__('Version "%s" could not be removed because it is last public version for its page.', $object->getLabel())
            );
        }

        /*
         * Checking if Version does not contain published revision
         */
        $select = $this->_getReadAdapter()->select();
        $select->from(array('p' => $this->getTable('cms/page')), array())
            ->where('p.page_id = ?', $object->getPageId())
            ->join(array('r' => $this->getTable('enterprise_cms/page_revision')),
                'r.revision_id = p.published_revision_id', array('r.version_id'));

        $result = $this->_getReadAdapter()->fetchOne($select);

        if ($result == $object->getVersionId()) {
            Mage::throwException(
                Mage::helper('enterprise_cms')->__('Version "%s" could not be removed because its revision has beed published.', $object->getLabel())
            );
        }
    }
}
