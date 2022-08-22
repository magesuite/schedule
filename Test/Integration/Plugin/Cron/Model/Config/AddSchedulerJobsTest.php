<?php

namespace MageSuite\Schedule\Test\Integration\Plugin\Cron\Model\Config;

/**
 * @magentoAppArea adminhtml
 */
class AddSchedulerJobsTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;

    protected ?\Magento\Cron\Model\Config $cronConfig;

    protected ?\MageSuite\Schedule\Model\Schedule\Jobs $scheduleJobs;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->cronConfig = $this->objectManager->get(\Magento\Cron\Model\Config::class);
        $this->scheduleJobs = $this->objectManager->get(\MageSuite\Schedule\Model\Schedule\Jobs::class);

        $this->scheduleJobs = $this->createStub(\MageSuite\Schedule\Model\Schedule\Jobs::class);
        $this->objectManager->addSharedInstance($this->scheduleJobs, \MageSuite\Schedule\Model\Schedule\Jobs::class);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testItAddsSchedulers()
    {
        $schedulerId = 3;
        $cronExpression = '10 03 * * *';

        $this->scheduleJobs->method('execute')->willReturn([
            [
                'id' => $schedulerId,
                'cron_expression' => $cronExpression
            ]
        ]);

        $jobs = $this->cronConfig->getJobs();

        $this->assertArrayHasKey('scheduler', $jobs);
        $schedulerGroup = $jobs['scheduler'];

        $schedulerJobCode = sprintf(\MageSuite\Schedule\Helper\Configuration::CRON_JOB_METHOD_FORMAT, 'general', $schedulerId);

        $this->assertArrayHasKey($schedulerJobCode, $schedulerGroup);
        $this->assertEquals($schedulerJobCode, $schedulerGroup[$schedulerJobCode]['name']);
        $this->assertEquals('MageSuite\Schedule\Cron\Process', $schedulerGroup[$schedulerJobCode]['instance']);
        $this->assertEquals($schedulerJobCode, $schedulerGroup[$schedulerJobCode]['method']);
        $this->assertEquals($cronExpression, $schedulerGroup[$schedulerJobCode]['schedule']);
    }
}
