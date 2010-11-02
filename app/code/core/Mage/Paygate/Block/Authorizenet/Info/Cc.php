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
 * @category    Mage
 * @package     Mage_Paygate
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Paygate_Block_Authorizenet_Info_Cc extends Mage_Payment_Block_Info
{
    /**
     * Cards info block
     *
     * @return string
     */
    public function getCardsInfoBlock()
    {
        return $this->_createBlock('paygate/authorizenet_cards')
            ->setMethod($this->getMethod());
    }

    /**
     * Create block
     *
     * @param string $blockType
     * @return mixed
     */
    protected function _createBlock($blockType)
    {
        if ($this->getLayout()) {
            return $this->getLayout()->createBlock($blockType);
        }
        else {
            $className = Mage::getConfig()->getBlockClassName($blockType);
            return new $className;
        }
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $this->setChild('cards_info_block', $this->getCardsInfoBlock());
        return parent::_toHtml();
    }
}
