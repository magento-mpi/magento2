<?php
/**
 * {license_notice}
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Container for display configuration data
 *
 * @category   Saas
 * @package    Saas_PrintedTemplate
 * @subpackage Models
 */
class Saas_PrintedTemplate_Model_Variable_Pagination extends Saas_PrintedTemplate_Model_Variable_Abstract
{
    /**
     * Constructor
     *
     * @see Saas_PrintedTemplate_Model_Template_Variable_Abstract::__construct()
     * @param Magento_Sales_Model_Order_Payment $value
     */
    public function __construct($value = null)
    {
        parent::__construct($value);
        $this->_setListsFromConfig('pagination');
    }

    /**
     * Get current page number placeholder
     *
     * @return string
     */
    public function getPageNumber()
    {
        // use long class name to avoid class name duplicates in document
        return '<span class="printed_template_page_number"></span>';
    }

    /**
     * Get pages total number placeholder
     *
     * @return string
     */
    public function getPageTotal()
    {
        // use long class name to avoid class name duplicates in document
        return '<span class="printed_template_page_total"></span>';
    }
}
