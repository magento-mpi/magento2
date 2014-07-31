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
        $userContexts = array_filter(
            $userContexts,
            function ($item) {
                return isset($item['type']) && isset($item['sortOrder']);
            }
        );

        uasort($userContexts, array($this, 'compareContextsSortOrder'));

        foreach ($userContexts as $userContext) {
            $this->add($userContext['type']);
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
        return $this->getUserContext() ? $this->getUserContext()->getUserType() : null;
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

    /**
     * Compare contexts sortOrder
     *
     * @param array $contextDataFirst
     * @param array $contextDataSecond
     * @return int
     */
    protected function compareContextsSortOrder($contextDataFirst, $contextDataSecond)
    {
        if ((int)$contextDataFirst['sortOrder'] == (int)$contextDataSecond['sortOrder']) {
            return 0;
        }

        if ((int)$contextDataFirst['sortOrder'] < (int)$contextDataSecond['sortOrder']) {
            return -1;
        } else {
            return 1;
        }
    }
}
