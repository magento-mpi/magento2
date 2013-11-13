<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Integration\Model;

/**
 * Integration model.
 *
 * @method \string getName()
 * @method Integration setName(\string $name)
 * @method \string getEmail()
 * @method Integration setEmail(\string $email)
 * @method \int getStatus()
 * @method Integration setStatus(\int $value)
 * @method \int getAuthentication()
 * @method Integration setAuthentication(\int $value)
 * @method \string getEndpoint()
 * @method Integration setEndpoint(\string $endpoint)
 * @method \string getCreatedAt()
 * @method Integration setCreatedAt(\string $createdAt)
 * @method \string getUpdatedAt()
 * @method Integration setUpdatedAt(\string $createdAt)
 */
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
     * @return Integration
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        if ($this->isObjectNew()) {
            $this->setCreatedAt($this->_getResource()->formatDate(true));
        }
        $this->setUpdatedAt($this->_getResource()->formatDate(true));
        return $this;
    }
}
