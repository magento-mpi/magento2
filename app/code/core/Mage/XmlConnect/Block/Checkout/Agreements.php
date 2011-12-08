<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout agreements xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Checkout_Agreements extends Mage_Checkout_Block_Agreements
{
    /**
     * Render agreements xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $agreementsXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element', '<agreements></agreements>');
        if ($this->getAgreements()) {
            foreach ($this->getAgreements() as $agreement) {
                $itemXmlObj = $agreementsXmlObj->addChild('item');
                $content = $agreementsXmlObj->xmlentities($agreement->getContent());
                if (!$agreement->getIsHtml()) {
                    $content = nl2br(strip_tags($content));
                }
                $agreementText = $agreementsXmlObj->xmlentities($agreement->getCheckboxText());
                $itemXmlObj->addChild('label', $agreementText);
                $itemXmlObj->addChild('content', $content);
                $itemXmlObj->addChild('code', 'agreement[' . $agreement->getId() . ']');
                $itemXmlObj->addChild('agreement_id', $agreement->getId());
            }
        }

        return $agreementsXmlObj->asNiceXml();
    }
}
