<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Model;

/**
 * @method \Magento\CheckoutAgreements\Model\Resource\Agreement _getResource()
 * @method \Magento\CheckoutAgreements\Model\Resource\Agreement getResource()
 * @method string getName()
 * @method \Magento\CheckoutAgreements\Model\Agreement setName(string $value)
 * @method string getContent()
 * @method \Magento\CheckoutAgreements\Model\Agreement setContent(string $value)
 * @method string getContentHeight()
 * @method \Magento\CheckoutAgreements\Model\Agreement setContentHeight(string $value)
 * @method string getCheckboxText()
 * @method \Magento\CheckoutAgreements\Model\Agreement setCheckboxText(string $value)
 * @method int getIsActive()
 * @method \Magento\CheckoutAgreements\Model\Agreement setIsActive(int $value)
 * @method int getIsHtml()
 * @method \Magento\CheckoutAgreements\Model\Agreement setIsHtml(int $value)
 *
 */
class Agreement extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CheckoutAgreements\Model\Resource\Agreement');
    }

    /**
     * @param \Magento\Framework\Object $agreementData
     * @return array|bool
     */
    public function validateData($agreementData)
    {
        $errors = [];

        if ($agreementData->getContentHeight() !== ''
            && !preg_match('/^[0-9]*\.*[0-9]+(px|pc|pt|ex|em|mm|cm|in|%)$/', $agreementData->getContentHeight())
        ) {
            $errors[] = "Please enter correct value for 'Content Height' field with units [px,pc,pt,ex,em,mm,cm,in,%].";
        }

        return (count($errors)) ? $errors : true;
    }
}
