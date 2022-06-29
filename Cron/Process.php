<?php
namespace MageSuite\Schedule\Cron;

class Process
{
    const LOCK_NAME = 'magesuite_scheduler';

    protected $handlerClass = \MageSuite\Schedule\Model\Queue\SchedulerHandler::class;

    protected \MageSuite\Schedule\Helper\Configuration $configuration;

    protected \MageSuite\Queue\Service\Publisher $queuePublisher;

    protected \MageSuite\Schedule\Model\SchedulerJobsCollector $schedulerJobsCollector;

    protected \Magento\Framework\Lock\Backend\Database $databaseLocker;

    public function __construct(
        \MageSuite\Schedule\Helper\Configuration $configuration,
        \MageSuite\Queue\Service\Publisher $queuePublisher,
        \MageSuite\Schedule\Model\SchedulerJobsCollector $schedulerJobsCollector,
        \Magento\Framework\Lock\Backend\Database $databaseLocker
    ) {
        $this->configuration = $configuration;
        $this->queuePublisher = $queuePublisher;
        $this->schedulerJobsCollector = $schedulerJobsCollector;
        $this->databaseLocker = $databaseLocker;
    }

    /**
     * etc/crontab.xml does not allow passing arguments to invoked methods
     * so for executing scheduler logic we use part of invoked method name as scheduler identifier
     * calling magesuite_scheduler_2 will invoke scheduler with id: 2
     * @param $name
     * @param array $arguments
     */
    public function __call($name, array $arguments)
    {
        if (!$this->configuration->isEnabled()) {
            return;
        }

        $this->waitUntilUnlocked();

        if (preg_match('/scheduler_([a-zA-Z0-9]*)_([0-9+])/', $name)) {
            $this->process($name);
        }

        $this->databaseLocker->unlock(self::LOCK_NAME);
    }

    protected function process($name)
    {
        $method = $this->configuration->getSchedulingMethod();
        $parameters = $this->getParameters($name);

        switch ($method) {
            case \MageSuite\Schedule\Model\Source\SchedulingMethod::METHOD_RABBITMQ:
                $this->queuePublisher->publish($this->handlerClass, $parameters);
                break;
            default:
                $groupProcessor = $this->schedulerJobsCollector->getGroupProcessorByGroupName($parameters->getGroupName());
                $groupProcessor->execute($parameters->getSchedulerId());
                break;
        }
    }

    protected function getParameters($name)
    {
        $jobCodeParts = explode('_', $name);

        list($groupName, $schedulerId) = array_slice($jobCodeParts, -2);

        return new \Magento\Framework\DataObject([
            'group_name' => $groupName,
            'scheduler_id' => $schedulerId
        ]);
    }

    public function execute() //phpcs:ignore
    {
    }

    protected function waitUntilUnlocked()
    {
        while ($this->databaseLocker->isLocked(self::LOCK_NAME)) {
            sleep(1); //phpcs:ignore
        }

        $this->databaseLocker->lock(self::LOCK_NAME);
    }
}
