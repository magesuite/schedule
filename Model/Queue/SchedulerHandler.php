<?php
namespace MageSuite\Schedule\Model\Queue;

class SchedulerHandler implements \MageSuite\Queue\Api\Queue\HandlerInterface
{
    protected \MageSuite\Schedule\Model\SchedulerJobsCollector $schedulerJobsCollector;

    protected \MageSuite\Schedule\Logger\Logger $logger;

    public function __construct(
        \MageSuite\Schedule\Model\SchedulerJobsCollector $schedulerJobsCollector,
        \MageSuite\Schedule\Logger\Logger $logger
    ) {
        $this->schedulerJobsCollector = $schedulerJobsCollector;
        $this->logger = $logger;
    }

    public function execute($data)
    {
        try {
            $groupProcessor = $this->schedulerJobsCollector->getGroupProcessorByGroupName($data->getGroupName());
            $groupProcessor->execute($data->getSchedulerId());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
    }
}
