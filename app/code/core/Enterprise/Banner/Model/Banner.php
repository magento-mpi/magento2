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
 * @package    Enterprise_Banner
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Banner_Model_Banner extends Mage_Core_Model_Abstract
{
    /**
     * Enter description here...
     *
     */
    const STATUS_ENABLED = 0;
    /**
     * Enter description here...
     *
     */
    const STATUS_DISABLED  = 1;

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $_eventPrefix = 'enterprise_banner';

    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $_eventObject = 'banner';

    /**
     * Initialize banner model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_banner/banner');
    }

    /**
     * Enter description here...
     *
     */
    protected function _beforeSave()
    {
        $banner_name = $this->getBannerName();
        if (empty($banner_name)) {
            Mage::throwException(Mage::helper('enterprise_banner')->__('Banner name must be specified'));
        }
        // content
        return parent::_beforeSave();
    }

    /**
     * Enter description here...
     *
     */
    protected function _afterSave()
    {
        parent::_afterSave();
    }

    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function _afterLoad()
    {
        return parent::_afterLoad();
    }

}
