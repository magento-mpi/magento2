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
 * Cms page version model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Model_Page_Version extends Mage_Core_Model_Abstract
{
    /**
     * Access level constants
     */
    const ACCESS_LEVEL_PRIVATE = 'private';
    const ACCESS_LEVEL_PROTECTED = 'protected';
    const ACCESS_LEVEL_PUBLIC = 'public';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_init('enterprise_cms/page_version');
    }

    /**
     * Preparing data before save
     *
     * @return Enterprise_Cms_Model_Revision
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            /*
             * Preparing new human-readable id
             */
            $level = 0;

            $incrementModel = Mage::getModel('enterprise_cms/increment')
                ->loadByTypeNodeLevel(0, $this->getPageId(), $level);

            if (!$incrementModel->getId()) {
                $incrementModel->setType(0)
                    ->setNode($this->getPageId())
                    ->setLevel($level);
            }

            $incrementNumber = $incrementModel->getNextId();
            $incrementModel->setLastId($incrementNumber)
                ->save();

            $this->setVersionNumber($incrementNumber);
        }

        if (!$this->getLabel()) {
            Mage::throwException(Mage::helper('enterprise_cms')->__('Label for version is required field.'));
        }

        return parent::_beforeSave();
    }

    /**
     * Processing some data after version saved
     *
     * @return Enterprise_Cms_Model_Page_Version
     */
    protected function _afterSave()
    {
        // If this was a new version we should create initial revision for it
        // from specified revision or from latest for parent version
        if (!$this->getOrigData($this->getIdFieldName())) {
            $revision = Mage::getModel('enterprise_cms/page_revision');

            $revision->setUserId(Mage::getSingleton('admin/session')->getUser()->getId());
            $revision->setAccessLevel(Mage::getSingleton('enterprise_cms/config')->getAllowedAccessLevel());

            if ($this->getInitialRevisionId()) {
                $revision->load($this->getInitialRevisionId());
            } else {
                $revision->load($this->getOrigData($this->getIdFieldName()), 'version_id');
            }

            $revision->setVersionId($this->getId())
                ->setUserId($this->getUserId())
                ->save();
        }
    }
}
