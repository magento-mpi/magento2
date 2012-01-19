<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Logging helper
 */
class Enterprise_Logging_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Join array into string except empty values
     *
     * @param array $array Array to join
     * @param string $glue Separator to join
     * @return string
     */
    public function implodeValues($array, $glue = ', ')
    {
        if (!is_array($array)) {
            return $array;
        }
        $result = array();
        foreach ($array as $item) {
            if (is_array($item)) {
                $result[] = $this->implodeValues($item);
            }
            else {
                if ((string)$item !== '') {
                    $result[] = $item;
                }
            }
        }
        return implode($glue, $result);
    }

    /**
     * Get translated label by logging action name
     *
     * @param string $action
     * @return string
     */
    public function getLoggingActionTranslatedLabel($action)
    {
        /**
         * @var Mage_Core_Model_Config_Element $actionNode
         */
        $actionNode = Mage::getConfig()->getNode('global/logging_actions/' . $action);

        if (!$actionNode) {
            return $action;
        }

        $actionNodeArray = $actionNode->asArray();

        if (!isset($actionNodeArray['label'])) {
            return $action;
        }

        if (!empty($actionNodeArray['@']['module'])) {
            $helper = Mage::helper($actionNodeArray['@']['module']);
        }

        if (empty($helper)) {
            $helper = $this;
        }

        return $helper->__($actionNodeArray['label']);
    }
}
