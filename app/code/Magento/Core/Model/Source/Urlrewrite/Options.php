<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * UrlRewrite Options source model
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Source\Urlrewrite;

class Options implements \Magento\Core\Model\Option\ArrayInterface
{
    const TEMPORARY = 'R';
    const PERMANENT = 'RP';

    /**
     * @var array|null
     */
    protected $_options = null;

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                ''              => __('No'),
                self::TEMPORARY => __('Temporary (302)'),
                self::PERMANENT => __('Permanent (301)')
            );
        }
        return $this->_options;
    }

    /**
     * Get options list (redirects only)
     *
     * @return array
     */
    public function getRedirectOptions()
    {
        return array(self::TEMPORARY, self::PERMANENT);
    }

    /**
     * Return option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}
