<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Front end helper block to add links
 *
 * @category   Enterprise
 * @package    Enterprise_Invitation
 */
class Enterprise_Invitation_Block_Link extends Mage_Core_Block_Template
{
    /**
     * Adding link to account links block link params if invitation
     * is allowed globaly and for current website
     *
     * @param string $block
     * @param string $name
     * @param string $path
     * @param string $label
     * @param array $urlParams
     * @return Enterprise_Invitation_Block_Customer_Link
     */
    public function addAccountLink($block, $label, $url='', $title='', $prepare=false, $urlParams=array(),
        $position=null, $liParams=null, $aParams=null, $beforeText='', $afterText='')
    {
        if (Mage::getSingleton('Enterprise_Invitation_Model_Config')->isEnabledOnFront()) {
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($label, $url, $title, $prepare, $urlParams,
                    $position, $liParams, $aParams, $beforeText, $afterText);
            }
        }
        return $this;
    }

    /**
     * Adding link to account links block link params if invitation
     * is allowed globaly and for current website
     *
     * @param string $block
     * @param string $name
     * @param string $path
     * @param string $label
     * @param array $urlParams
     * @return Enterprise_Invitation_Block_Customer_Link
     */
    public function addDashboardLink($block, $name, $path, $label, $urlParams = array())
    {
        if (Mage::getSingleton('Enterprise_Invitation_Model_Config')->isEnabledOnFront()) {
            $blockInstance = $this->getLayout()->getBlock($block);
            if ($blockInstance) {
                $blockInstance->addLink($name, $path, $label, $urlParams);
            }
        }
        return $this;
    }
}
