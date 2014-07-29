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
    protected $chosenUserContext;

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
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return $this->getUserContext() ? $this->getUserContext()->getUserId() : null;
    }

    /**
     * {@inheritdoc}
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
        if (is_null($this->chosenUserContext)) {
            /** @var UserContextInterface $userContext */
            foreach ($this->userContexts as $userContext) {
                if ($userContext->getUserType() && !is_null($userContext->getUserId())) {
                    $this->chosenUserContext = $userContext;
                    break;
                }
            }
            if (is_null($this->chosenUserContext)) {
                $this->chosenUserContext = false;
            }
        }
        return $this->chosenUserContext;
    }
}
