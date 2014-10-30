<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Data;

class CustomerSecure
{
    /**
     * @var string
     */
    protected $rpToken;

    /**
     * @var string
     */
    protected $rpTokenCreatedAt;

    /**
     * @var string
     */
    protected $hashedPassword;

    /**
     * @var string
     */
    protected $deleteable;

    /**
     * Get reset password token
     *
     * @return string|null
     */
    public function getRpToken()
    {
        return $this->rpToken;
    }

    /**
     * Get reset password token creation time
     *
     * @return string|null
     */
    public function getRpTokenCreatedAt()
    {
        return $this->rpTokenCreatedAt;
    }

    /**
     * Set reset password token
     *
     * @param string $rpToken
     * @return $this
     */
    public function setRpToken($rpToken)
    {
        $this->rpToken = $rpToken;
        return $this;
    }

    /**
     * Set reset password token creation time
     *
     * @param string $rpTokenCreatedAt
     * @return $this
     */
    public function setRpTokenCreatedAt($rpTokenCreatedAt)
    {
        $this->rpTokenCreatedAt = $rpTokenCreatedAt;
        return $this;
    }

    /**
     * Get hashed password
     *
     * @return string|null
     */
    public function getPasswordHash()
    {
        return $this->hashedPassword;
    }

    /**
     * Set hashed password
     *
     * @param string $hashedPassword
     * @return $this
     */
    public function setPasswordHash($hashedPassword)
    {
        $this->hashedPassword = $hashedPassword;
        return $this;
    }

    /**
     * Returns true if customer can be deleted
     *
     * @return bool
     */
    public function isDeleteable()
    {
        return $this->deleteable;
    }

    /**
     * Set true if customer can be deleted
     *
     * @param string $deleteable
     * @return bool
     */
    public function setDeleteable($deleteable)
    {
        return $this->deleteable = $deleteable;
    }
}

