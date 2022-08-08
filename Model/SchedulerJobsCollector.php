<?php
namespace MageSuite\Schedule\Model;

class SchedulerJobsCollector
{
    protected $jobsGroups;

    public function __construct(array $jobsGroups)
    {
        $this->jobsGroups = $jobsGroups;
    }

    public function getGroupProcessorByGroupName($groupName)
    {
        foreach ($this->jobsGroups as $jobGroupName => $jobsGroup) {
            if ($groupName != $jobGroupName) {
                continue;
            }

            return $jobsGroup['processor'];
        }

        return null;
    }

    public function getAllJobsGroupedByGroups()
    {
        if (empty($this->jobsGroups)) {
            return [];
        }

        $jobs = [];

        foreach ($this->jobsGroups as $groupName => $jobsGroup) {
            if (!$jobsGroup['cronjob'] instanceof \MageSuite\Schedule\Model\Schedule\JobsGroupInterface) {
                continue;
            }

            $jobs[$groupName] = $jobsGroup['cronjob']->execute();
        }

        return $jobs;
    }
}
