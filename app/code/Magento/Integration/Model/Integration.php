<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Integration model
 *
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

    /**
     * Prepare data to be saved to database
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->_getResource()->formatDate(true));
        } elseif ($this->getId()) {
            $this->setUpdatedAt($this->_getResource()->formatDate(true));
        }
        return $this;
    }

    /**
     * Load Integration by name.
     *
     * @param string $name
     * @return \Magento\Integration\Model\Integration
     */
    public function loadByName($name)
    {
        return $this->load($name, 'name');
    }
}
