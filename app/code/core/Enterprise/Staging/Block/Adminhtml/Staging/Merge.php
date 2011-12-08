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
 * Staging merge setting block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Staging_Merge extends Mage_Adminhtml_Block_Widget
{
    /**
     * merge settinggs blocks
     *
     * @var array
     */
    private $_mergeSettingsBlock = array();

    /**
     * merge settings block types
     *
     * @var array
     */
    private $_mergeSettingsBlockTypes = array();

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

    /**
     * get merge setting blocks
     *
     * @param string $stagingType
     * @return mixed
     */
    protected function _getMergeSettingsBlock($stagingType)
    {
        if (!isset($this->_mergeSettingsBlock[$stagingType])) {
            if (isset($this->_mergeSettingsBlockTypes[$stagingType])
                && $this->_mergeSettingsBlockTypes[$stagingType]['block'] != '') {
                    $block = $this->_mergeSettingsBlockTypes[$stagingType]['block'];
                    $this->_mergeSettingsBlock[$stagingType] = $this->getLayout()->createBlock($block);
            } else {
                return false;
            }
        }
        return $this->_mergeSettingsBlock[$stagingType];
    }

    /**
     * get merge setting block template
     *
     * @param string $stagingType
     * @return mixed
     */
    protected function _getMergeSettingsBlockTemplate($stagingType)
    {
        if (isset($this->_mergeSettingsBlockTypes[$stagingType])
            && $this->_mergeSettingsBlockTypes[$stagingType]['template'] != '') {
                return $this->_mergeSettingsBlockTypes[$stagingType]['template'];
        }
        return false;
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
        if (!$staging->getType()) {
            $staging->setType('website');
        }
        $block = $this->_getMergeSettingsBlock($staging->getType());
        $template = $this->_getMergeSettingsBlockTemplate($staging->getType());
        if ($block && $template) {
            return $block
                ->setTemplate($template)
                ->setStaging($staging)
                ->setIdSuffix($idSuffix)
                ->toHtml();
        }

        return '';
    }

    /**
     * return html structure of merge
     *
     * @return string
     */
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
