<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging merge setting block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Merge extends Mage_Adminhtml_Block_Widget
{
	private $_mergeSettingsBlock = array();
    private $_mergeSettingsBlockDefaultTemplate = 'enterprise/staging/merge/settings.phtml';
    private $_mergeSettingsBlockTypes = array();

    public function __construct()
    {
        parent::__construct();
        //$this->setTemplate('enterprise/staging/manage/staging/merge.phtml');
        //$this->setId('enterprise_staging_merge');
    }

    /**
     * Retrieve currently edited staging object
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

    protected function _getMergeSettingsBlock($stagingType)
    {
        if (!isset($this->_mergeSettingsBlock[$stagingType])) {
            $block = 'enterprise_staging/staging_merge_settings';
            if (isset($this->_mergeSettingsBlockTypes[$stagingType])) {
                if ($this->_mergeSettingsBlockTypes[$stagingType]['block'] != '') {
                    $block = $this->_mergeSettingsBlockTypes[$stagingType]['block'];
                }
            }
            $this->_mergeSettingsBlock[$stagingType] = $this->getLayout()->createBlock($block);
        }
        return $this->_mergeSettingsBlock[$stagingType];
    }

    protected function _getMergeSettingsBlockTemplate($stagingType)
    {
        if (isset($this->_mergeSettingsBlockTypes[$stagingType])) {
            if ($this->_mergeSettingsBlockTypes[$stagingType]['template'] != '') {
                return $this->_mergeSettingsBlockTypes[$stagingType]['template'];
            }
        }
        return $this->_mergeSettingsBlockTypes;
    }

    /**
     * Returns staging merge settings block html
     *
     * @param Mage_Catalog_Model_Product $staging
     * @param boolean $displayMinimalPrice
     */
    public function getMergeSettingsHtml($staging = null, $idSuffix='')
    {
    	if (is_null($staging)) {
    		$staging = $this->getStaging();
    	}
        return $this->_getMergeSettingsBlock($staging->getType())
            ->setTemplate($this->_getMergeSettingsBlockTemplate($staging->getType()))
            ->setStaging($staging)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }

    protected function _toHtml()
    {
    	return $this->getMergeSettingsHtml();
    }

    /**
     * Adding customized merge settings block for staging type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     */
    public function addMergeSettingsBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_mergeSettingsBlockTypes[$type] = array(
                'block'     => $block,
                'template'  => $template
            );
        }
    }
}