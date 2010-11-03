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

class Mage_Paygate_Block_Authorizenet_Cards extends Mage_Payment_Block_Form
{
    /**
     * Set block template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('paygate/cards.phtml');
    }

    /**
     * Render as PDF
     *
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('paygate/info/pdf.phtml');
        return $this->toHtml();
    }

    /**
     * Retrieve credit card type name
     *
     * @param string $ccType
     * @return string
     */
    public function getCcTypeName($ccType)
    {
        $types = Mage::getSingleton('payment/config')->getCcTypes();
        if (isset($types[$ccType])) {
            return $types[$ccType];
        }
        return (empty($ccType)) ? Mage::helper('payment')->__('N/A') : $ccType;
    }

    /**
     * Retrieve credit cards info
     *
     * @return array
     */
    public function getCards()
    {
        $cardsInfo = $this->getMethod()->getCardsInstance()->getCards();
        $cards = array();

        if (is_array($cardsInfo)) {
            foreach ($cardsInfo as $key => $card) {
                $data = array();
                if (isset($card['cc_type'])) {
                    $data[] = array(
                        'label'  => Mage::helper('paygate')->__('Credit Card Type'),
                         'value' => $this->getCcTypeName($card['cc_type'])
                    );
                }
                if (isset($card['cc_number'])) {
                    $data[] = array(
                        'label' => Mage::helper('paygate')->__('Credit Card Number'),
                        'value' => $card['cc_number']
                    );
                }
                if (isset($card['authorized_amount'])) {
                    $data[] = array(
                        'label' => Mage::helper('paygate')->__('Processed Amount'),
                        'value' => Mage::helper('core')->currency($card['authorized_amount'])
                    );
                }
                if (isset($card['balance_on_card']) && is_numeric($card['balance_on_card'])) {
                    $data[] = array(
                        'label' => Mage::helper('paygate')->__('Remaining Balance'),
                        'value' => Mage::helper('core')->currency($card['balance_on_card'])
                    );
                }
                $cards[] = $data;
            }
        }
        return $cards;
    }
}
