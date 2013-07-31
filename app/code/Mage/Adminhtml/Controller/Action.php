<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic backend controller
 */
class Mage_Adminhtml_Controller_Action extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'adminhtml';

    /**
     * Translate a phrase
     *
     * @return string
     */
    public function __()
    {
        return $this->_translator->translate(func_get_args());
    }
}
