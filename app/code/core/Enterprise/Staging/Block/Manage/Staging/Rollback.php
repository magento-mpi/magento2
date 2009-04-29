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
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Staging rollback setting block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Rollback extends Mage_Adminhtml_Block_Widget
{
    /**
     * Rollback settings block list
     *
     * @var array
     */
    private $_rollbackSettingsBlock = array();

    /**
     * rollback setting block template
     *
     * @var string
     */
    private $_rollbackSettingsBlockDefaultTemplate = 'enterprise/staging/rollback/settings.phtml';

    /**
     * rollback setting block types
     *
     * @var array
     */
    private $_rollbackSettingsBlockTypes = array();

    /**
     * Retrieve event
     *
     * @return Enterprise_Staging_Block_Manage_Staging-Event
     */
    public function getEvent()
    {
        if (!($this->getData('staging_event') instanceof Enterprise_Staging_Model_Staging_Event)) {
            $this->setData('staging_event', Mage::registry('staging_event'));
        }
        return $this->getData('staging_event');
    }

    /**
     * Retrieve staging object of current event
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        return $this->getEvent()->getStaging();
    }

    /**
     * get rollback setting blocks
     *
     * @param string $stagingType
     * @return array
     */
    protected function _getRollbackSettingsBlock($stagingType)
    {
        if (!isset($this->_rollbackSettingsBlock[$stagingType])) {
            $block = 'enterprise_staging/staging_rollback_settings';
            if (isset($this->_rollbackSettingsBlockTypes[$stagingType])) {
                if ($this->_rollbackSettingsBlockTypes[$stagingType]['block'] != '') {
                    $block = $this->_rollbackSettingsBlockTypes[$stagingType]['block'];
                }
            }
            $this->_rollbackSettingsBlock[$stagingType] = $this->getLayout()->createBlock($block);
        }
        return $this->_rollbackSettingsBlock[$stagingType];
    }

    /**
     * get rollback settings block types
     *
     * @param string $stagingType
     * @return array
     */
    protected function _getRollbackSettingsBlockTemplate($stagingType)
    {
        if (isset($this->_rollbackSettingsBlockTypes[$stagingType])) {
            if ($this->_rollbackSettingsBlockTypes[$stagingType]['template'] != '') {
                return $this->_rollbackSettingsBlockTypes[$stagingType]['template'];
            }
        }
        return $this->_rollbackSettingsBlockTypes;
    }

    /**
     * Returns staging rollback settings block html
     *
     * @param Mage_Catalog_Model_Product $staging
     * @param boolean $displayMinimalPrice
     */
    public function getRollbackSettingsHtml($staging = null, $idSuffix='')
    {
        if (is_null($staging)) {
            $staging = $this->getStaging();
        }
        return $this->_getRollbackSettingsBlock($staging->getType())
            ->setTemplate($this->_getRollbackSettingsBlockTemplate($staging->getType()))
            ->setStaging($staging)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }

    /**
     * get rollbacl content as html
     *
     * @return unknown
     */
    protected function _toHtml()
    {
        return $this->getRollbackSettingsHtml();
    }

    /**
     * Adding customized rollback settings block for staging type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     */
    public function addRollbackSettingsBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_rollbackSettingsBlockTypes[$type] = array(
                'block'     => $block,
                'template'  => $template
            );
        }
    }
}
