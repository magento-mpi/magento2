<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_OAuth
 */

/**
 * OAuth authorization styles block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Block_Authorize_Style extends Mage_OAuth_Block_Authorize_Abstract
{
    /**
     * Set default data
     */
    public function __construct()
    {
        parent::__construct();

        //default load template from admin package
        $this->setIsSimple(true);
    }

}
