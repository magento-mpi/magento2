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
 * Cms page revision model
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Model_Page_Revision extends Mage_Core_Model_Abstract
{
    /**
     * Configuration model
     * @var Enterprise_Cms_Model_Config
     */
    protected $_config;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_init('enterprise_cms/page_revision');
        $this->_config = Mage::getSingleton('enterprise_cms/config');
    }

    /**
     * Preparing data before save
     *
     * @return Enterprise_Cms_Model_Revision
     */
    protected function _beforeSave()
    {
        /*
         * Reseting revision id this revision should be saved as new.
         * Bc data was changed or original version id not equals to new version id.
         */
        if ($this->_revisionedDataWasModified() || $this->getVersionId() != $this->getOrigData('version_id')) {
            $this->unsetData($this->getIdFieldName());
            $this->setCreatedAt(Mage::getSingleton('core/date')->gmtDate());

            /*
             * Preparing new human-readable id
             */
            $level = 1;
            $incrementModel = Mage::getModel('enterprise_cms/increment')
                ->loadByTypeNodeLevel(0, $this->getVersionId(), $level);

            if (!$incrementModel->getId()) {
                $incrementModel->setType(0)
                    ->setNode($this->getVersionId())
                    ->setLevel($level);
            }

            $incrementNumber = $incrementModel->getNextId();
            $incrementModel->setLastId($incrementNumber)
                ->save();

            $this->setRevisionNumber($incrementNumber);
        }

        return parent::_beforeSave();
    }

    /**
     * Check data which is under revision control if it was modified.
     *
     * @return array
     */
    protected function _revisionedDataWasModified()
    {
        $format = Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
        $attributes = $this->_config->getPageRevisionControledAttributes();
        foreach ($attributes as $attr) {
            $value = $this->getData($attr);
            if ($this->getOrigData($attr) !== $value) {
                if ($this->getOrigData($attr) === NULL && $value === '' || $value === NULL) {
                    continue;
                }
                return true;
            }
        }
        return false;
    }

    /**
     * Prepare data which must be published
     *
     * @return array
     */
    protected function _prepareDataForPublish()
    {
        $data = array();
        $attributes = $this->_config->getPageRevisionControledAttributes();
        foreach ($this->getData() as $key => $value) {
            if (in_array($key, $attributes)) {
                $this->unsetData($key);
                $data[$key] = $value;
            }
        }

        $data['published_revision_id'] = $this->getId();

        return $data;
    }

    /**
     * Publishing current revision
     *
     * @return Enterprise_Cms_Model_Page_Revision
     */
    public function publish()
    {
        $this->_getResource()->beginTransaction();
        try {
            $data = $this->_prepareDataForPublish($this);
            $this->_getResource()->publish($data, $this->getPageId());
            $this->_getResource()->commit();
        } catch (Exception $e){
            $this->_getResource()->rollBack();
            throw $e;
        }
        return $this;
    }
}
