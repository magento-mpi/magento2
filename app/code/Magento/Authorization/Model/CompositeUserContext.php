<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Authorization\Model;

class CompositeUserContext implements \Magento\Authorization\Model\UserContextInterface
{
    /**
     * @var UserContextInterface[]
     */
    protected $userContexts = [];

    /**
     * @var UserContextInterface|bool
     */
    protected $userContext;

    /**
     * Register user contexts.
     *
     * @param UserContextInterface[] $userContexts
     */
    public function __construct($userContexts = [])
    {
        foreach ($userContexts as $userContext) {
            $this->add($userContext);
        }
    }

    /**
     * Add user context.
     *
     * @param UserContextInterface $userContext
     * @return CompositeUserContext
     */
    protected function add(UserContextInterface $userContext)
    {
        $this->userContexts[] = $userContext;
        return $this;
    }

    /**
     * Retrieve user id.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->getUserContext() ? $this->getUserContext()->getUserId() : null;
    }

    /**
     * Retrieve user type.
     *
     * @return string
     */
    public function getUserType()
    {
        return $this->getUserContext() ? $this->getUserContext()->getUserType() : '';
    }

    /**
     * Retrieve user context
     *
     * @return UserContextInterface|bool False if none of the registered user contexts can identify user type
     */
    protected function getUserContext()
    {
        if (is_null($this->userContext)) {
            /** @var UserContextInterface $userContext */
            foreach ($this->userContexts as $userContext) {
                if ($userContext->getUserType()) {
                    $this->userContext = $userContext;
                    break;
                }
            }
            if (is_null($this->userContext)) {
                $this->userContext = false;
            }
        }
        return $this->userContext;
    }
}
