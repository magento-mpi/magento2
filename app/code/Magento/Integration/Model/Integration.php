<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Integration model
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @method \Magento\Integration\Model\Resource\Integration _getResource()
 * @method \Magento\Integration\Model\Resource\Integration getResource()
 * @method \Magento\Integration\Model\Resource\Integration\Collection getCollection()
 * @method \Magento\Integration\Model\Resource\Integration\Collection getResourceCollection()
 * @method string getName()
 * @method \Magento\Integration\Model\Integration setName(string $name)
 * @method string getEmail()
 * @method \Magento\Integration\Model\Integration setEmail(string $email)
 * @method int getStatus()
 * @method \Magento\Integration\Model\Integration setStatus(int $value)
 * @method int getAuthentication()
 * @method \Magento\Integration\Model\Integration setAuthentication(int $value)
 * @method string getEndpoint()
 * @method \Magento\Integration\Model\Integration setEndpoint(string $endpoint)
 */
namespace Magento\Integration\Model;

class Integration extends \Magento\Core\Model\AbstractModel
{
    /**#@+
     * Integration statuses.
     */
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**#@-*/

    /**#@+
     * Authentication mechanism
     */
    const AUTHENTICATION_OAUTH = 1;
    const AUTHENTICATION_MANUAL = 2;
    /**#@-*/

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento\Integration\Model\Resource\Integration');
    }
}
