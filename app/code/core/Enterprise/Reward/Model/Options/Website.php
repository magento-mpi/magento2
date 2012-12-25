<?php
/**
 * {license_notice}
 *
 * @category    Enterprice
 * @package     Enterprice_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Websites option array
 *
 * @category   Enterprise
 * @package    Enterprise_Reward
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Reward_Model_Options_Website implements Mage_Core_Model_Option_ArrayInterface
{

    /**
     * Reward Source website model
     *
     * @var Enterprise_Reward_Model_Source_Website
     */
    protected $_sourceWebsiteModel;

    /**
     * @param Enterprise_Reward_Model_Source_Website $sourceWebsite
     */
    public function __construct(Enterprise_Reward_Model_Source_Website $sourceWebsite)
    {
        $this->_sourceWebsiteModel = $sourceWebsite;
    }

    /**
     * Return websites array
     * 
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_sourceWebsiteModel->toOptionArray();
    }
}
