<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paygate
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Paygate_Block_Authorizenet_Info_Cc extends Magento_Payment_Block_Info_Cc
{
    /**
     * Checkout progress information block flag
     *
     * @var bool
     */
    protected $_isCheckoutProgressBlockFlag = true;

    protected $_template = 'Magento_Paygate::info/cc.phtml';

    public function __construct(Magento_Core_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
    }

    /**
     * Render as PDF
     *
     * @return string
     */
    public function toPdf()
    {
        $this->setTemplate('Magento_Paygate::info/pdf.phtml');
        return $this->toHtml();
    }

    /**
     * Retrieve card info object
     *
     * @return mixed
     */
    public function getInfo()
    {
        if ($this->hasCardInfoObject()) {
            return $this->getCardInfoObject();
        }
        return parent::getInfo();
    }

    /**
     * Set checkout progress information block flag
     * to avoid showing credit card information from payment quote
     * in Previously used card information block
     *
     * @param bool $flag
     * @return Magento_Paygate_Block_Authorizenet_Info_Cc
     */
    public function setCheckoutProgressBlock($flag)
    {
        $this->_isCheckoutProgressBlockFlag = $flag;
        return $this;
    }

    /**
     * Retrieve credit cards info
     *
     * @return array
     */
    public function getCards()
    {
        $cardsData = $this->getMethod()->getCardsStorage()->getCards();
        $cards = array();

        if (is_array($cardsData)) {
            foreach ($cardsData as $cardInfo) {
                $data = array();
                if ($cardInfo->getProcessedAmount()) {
                    $amount = $this->_coreData->currency($cardInfo->getProcessedAmount(), true, false);
                    $data[__('Processed Amount')] = $amount;
                }
                if ($cardInfo->getBalanceOnCard() && is_numeric($cardInfo->getBalanceOnCard())) {
                    $balance = $this->_coreData->currency($cardInfo->getBalanceOnCard(), true, false);
                    $data[__('Remaining Balance')] = $balance;
                }
                $this->setCardInfoObject($cardInfo);
                $cards[] = array_merge($this->getSpecificInformation(), $data);
                $this->unsCardInfoObject();
                $this->_paymentSpecificInformation = null;
            }
        }
        if ($this->getInfo()->getCcType() && $this->_isCheckoutProgressBlockFlag) {
            $cards[] = $this->getSpecificInformation();
        }
        return $cards;
    }
}
