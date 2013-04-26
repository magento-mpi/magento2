<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Queue_QueueBackendInterface
{
    public function stopTask($taskName, $params)
    {
        $tasks = $this->getGearmanQueueResource()
            ->getTasksByName($taskName, $tenantId, self::TASK_STATUS_IS_PENDING);
        if (!$tasks) {
            return false;
        }
        foreach ($tasks as $task) {
            $this->getGearmanQueueResource()
                ->setTaskStatus($task['unique_key'], self::TASK_STATUS_SKIPPED);
        }
        return true;
    }

}
