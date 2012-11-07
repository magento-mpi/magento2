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
        /** @var $agreementsXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $agreementsXmlObj = Mage::getModel('Mage_XmlConnect_Model_Simplexml_Element',
            array('data' => '<agreements></agreements>'));
        if ($this->getAgreements()) {
            foreach ($this->getAgreements() as $agreement) {
                $itemXmlObj = $agreementsXmlObj->addChild('item');
                $content = $agreement->getContent();
                if (!$agreement->getIsHtml()) {
                    $content = nl2br($agreementsXmlObj->escapeXml($content));
                } else {
                    $agreementsXmlObj->xmlentities($content);
                }
                $agreementText = $agreementsXmlObj->escapeXml($agreement->getCheckboxText());
                $itemXmlObj->addChild('label', $agreementText);
                $itemXmlObj->addChild('content', $content);
                $itemXmlObj->addChild('code', 'agreement[' . $agreement->getId() . ']');
                $itemXmlObj->addChild('agreement_id', $agreement->getId());
            }
        }

        return $agreementsXmlObj->asNiceXml();
    }
}
