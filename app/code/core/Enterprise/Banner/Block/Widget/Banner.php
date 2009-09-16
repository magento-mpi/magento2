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
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Banner Widget Block
 *
 * @category   Enterprise
 * @package    Enterprise_Banner
 */
class Enterprise_Banner_Block_Widget_Banner
    extends Mage_Core_Block_Template
    implements Mage_Cms_Block_Widget_Interface
{
    /**
     * Display mode "fixed" flag
     *
     */
    const BANNER_WIDGET_DISPLAY_FIXED = 'fixed';

    /**
     * Display mode "salesrule" flag
     *
     */
    const BANNER_WIDGET_DISPLAY_SALESRULE = 'salesrule';

    /**
     * Display mode "catalogrule" flag
     *
     */
    const BANNER_WIDGET_DISPLAY_CATALOGRULE = 'catalogrule';

    /**
     * Rotation mode "series" flag: output one of banners sequentially per visitor session
     *
     */
    const BANNER_WIDGET_RORATE_SERIES = 'series';

    /**
     * Rotation mode "random" flag: output one of banners randomly
     *
     */
    const BANNER_WIDGET_RORATE_RANDOM = 'random';

    /**
     * Store Banner resource instance
     *
     * @var Enterprise_Banner_Model_Mysql4_Banner
     */
    protected $_bannerResource = null;

    /**
     * Store visitor session instance
     *
     * @var Mage_Core_Model_Session
     */
    protected $_sessionInstance = null;

    /**
     * Store current store ID
     *
     * @var int
     */
    protected $_currentStoreId = null;

    /**
     * Define default template, load Banner resource, get session instance and set current store ID
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate($this->getTemplate());
        $this->_bannerResource  = Mage::getResourceSingleton('enterprise_banner/banner');
        $this->_currentStoreId  = Mage::app()->getStore()->getId();
        $this->_sessionInstance = Mage::getSingleton('core/session');
    }

    /**
     * Set default display mode if it had not set
     *
     * @return string
     */
    public function getDisplayMode()
    {
        if (!$this->_getData('display_mode')) {
            $this->setData('display_mode', self::BANNER_WIDGET_DISPLAY_FIXED);
        }
        return $this->_getData('display_mode');
    }

    /**
     * Retrive converted to an array and filtered parameter "banner_ids"
     *
     * @return array
     */
    public function getBannerIds()
    {
        if (!$this->_getData('banner_ids')) {
            $this->setData('banner_ids', array(0));
        }
        else {
            $bannerIds = explode(',', $this->_getData('banner_ids'));
            foreach ($bannerIds as $_key => $_id) {
                $bannerIds[$_key] = (int)trim($_id);
            }
            $bannerIds = $this->_bannerResource->getExistingBannerIdsBySpecifiedIds($bannerIds);
            $this->setData('banner_ids', $bannerIds);
        }

        return $this->_getData('banner_ids');
    }

    /**
     * Retrieve right rotation mode or return null
     *
     * @return string|null
     */
    public function getRotate()
    {
        if (!$this->_getData('rotate') || ($this->_getData('rotate') != self::BANNER_WIDGET_RORATE_RANDOM && $this->_getData('rotate') != self::BANNER_WIDGET_RORATE_SERIES)) {
            $this->setData('rotate', null);
        }
        return $this->_getData('rotate');
    }

    /**
     * Get banner(s) content to display
     *
     * @return array
     */
    public function getBannersContent()
    {
        $banenrsContent = array();
        $bannerIds      = $this->getBannerIds();
        //Choose display mode
        switch ($this->getDisplayMode()) {
            case self::BANNER_WIDGET_DISPLAY_SALESRULE:
                $banenrsContent = self::BANNER_WIDGET_DISPLAY_SALESRULE;
                break;
            case self::BANNER_WIDGET_DISPLAY_CATALOGRULE:
                $banenrsContent = self::BANNER_WIDGET_DISPLAY_CATALOGRULE;
                break;
            case self::BANNER_WIDGET_DISPLAY_FIXED:
            default:
                $banenrsContent = $this->_getFixedBannersContent($bannerIds);
                break;
        }
        return $banenrsContent;
    }

    /**
     * Get banners content by specified banners IDs depend on Rotation mode
     *
     * @param array $bannerIds
     * @param int $storeId
     * @return array
     */
    protected function _getFixedBannersContent($bannerIds)
    {
        $bannersSequence = $content = array();
        if (!empty($bannerIds)) {
            //Choose rotation mode
            switch ($this->getRotate()) {
                case self::BANNER_WIDGET_RORATE_RANDOM :
                    $bannerId = $bannerIds[array_rand($bannerIds, 1)];
                    $content[$bannerId] = $this->_bannerResource->getStoreContent($bannerId, $this->_currentStoreId);
                    break;
                case self::BANNER_WIDGET_RORATE_SERIES :
                    $bannerId = $bannerIds[0];
                    if (!$this->_sessionInstance->hasBannersSequence()) {
                        $this->_sessionInstance->setBannersSequence(array($bannerIds[0]));
                    }
                    else {
                        $bannersSequence = $this->_sessionInstance->getBannersSequence();
                        $canShowIds = array_merge(array_diff($bannerIds, $bannersSequence), array());
                        if (!empty($canShowIds)) {
                            $bannersSequence[] = $canShowIds[0];
                            $bannerId = $canShowIds[0];
                        }
                        else {
                            $bannersSequence = array($bannerIds[0]);
                        }
                        $this->_sessionInstance->setBannersSequence($bannersSequence);
                    }
                    $content[$bannerId] = $this->_bannerResource->getStoreContent($bannerId, $this->_currentStoreId);
                    break;
                default:
                    $content = $this->_bannerResource->getBannersContent($bannerIds, $this->_currentStoreId);
                    break;
            }
        }
        return $content;
    }

}