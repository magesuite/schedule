<?php

namespace MageSuite\Schedule\Test\Integration\Cron;

/**
 * @magentoAppArea adminhtml
 */
class ProcessTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;

    protected ?\PHPUnit\Framework\MockObject\MockObject $configurationMock;

    protected ?\PHPUnit\Framework\MockObject\MockObject $schedulerJobsCollector;

    protected ?\MageSuite\Schedule\Cron\Process $process;

    protected function setUp(): void
    {
        parent::setUp();

        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->configurationMock = $this->getMockBuilder(\MageSuite\Schedule\Helper\Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->schedulerJobsCollector = $this->getMockBuilder(\MageSuite\Schedule\Model\SchedulerJobsCollector::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->process = $this->objectManager->create(
            \MageSuite\Schedule\Cron\Process::class,
            [
                'configuration' => $this->configurationMock,
                'schedulerJobsCollector' => $this->schedulerJobsCollector
            ]
        );
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testItExecutesCorrectMethods()
    {
        $this->configurationMock
            ->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configurationMock
            ->expects($this->once())
            ->method('getSchedulingMethod')
            ->willReturn(\MageSuite\Schedule\Model\Source\SchedulingMethod::METHOD_CRON);

        $this->schedulerJobsCollector
            ->expects($this->once())
            ->method('getGroupProcessorByGroupName')
            ->willReturn($this->objectManager->get(\MageSuite\Schedule\Service\Scheduler\Processor::class));

        $schedulerId = 3;
        $schedulerJobCode = sprintf(\MageSuite\Schedule\Helper\Configuration::CRON_JOB_METHOD_FORMAT, 'general', $schedulerId);

        $process = $this->objectManager->create(
            \MageSuite\Schedule\Cron\Process::class,
            [
                'configuration' => $this->configurationMock,
                'schedulerJobsCollector' => $this->schedulerJobsCollector
            ]
        );
        $process->{$schedulerJobCode}();
    }
}
