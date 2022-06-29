<?php
namespace MageSuite\Schedule\Plugin\Cron\Model\Config;

class AddSchedulerJobs
{
    protected \MageSuite\Schedule\Model\SchedulerJobsCollector $schedulerJobsCollector;

    /**
     * @var null|array
     */
    protected $schedulerJobs = null;

    public function __construct(\MageSuite\Schedule\Model\SchedulerJobsCollector $schedulerJobsCollector)
    {
        $this->schedulerJobsCollector = $schedulerJobsCollector;
    }

    public function afterGetJobs(\Magento\Cron\Model\Config $subject, $result)
    {
        $schedulerJobs = $this->getSchedulerJobs();

        if (empty($schedulerJobs)) {
            return $result;
        }

        $result[\MageSuite\Schedule\Helper\Configuration::CRON_GROUP_ID] = array_merge(
            $result[\MageSuite\Schedule\Helper\Configuration::CRON_GROUP_ID],
            $schedulerJobs
        );

        return $result;
    }

    protected function getSchedulerJobs()
    {
        if ($this->schedulerJobs !== null) {
            return $this->schedulerJobs;
        }

        $result = [];

        $schedulerJobsGroups = $this->schedulerJobsCollector->getAllJobsGroupedByGroups();

        if (empty($schedulerJobsGroups)) {
            return $result;
        }

        foreach ($schedulerJobsGroups as $groupName => $jobsGroup) {
            foreach ($jobsGroup as $job) {
                $methodName = sprintf(\MageSuite\Schedule\Helper\Configuration::CRON_JOB_METHOD_FORMAT, $groupName, $job['id']);

                $result[$methodName] = [
                    'name' => $methodName,
                    'instance' => \MageSuite\Schedule\Cron\Process::class,
                    'method' => $methodName,
                    'schedule' => $job['cron_expression']
                ];
            }

        }

        $this->schedulerJobs = $result;
        return $this->schedulerJobs;
    }
}
