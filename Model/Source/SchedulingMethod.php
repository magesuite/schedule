<?php
namespace MageSuite\Schedule\Model\Source;

class SchedulingMethod implements \Magento\Framework\Data\OptionSourceInterface
{
    const METHOD_CRON = 'cron';
    const METHOD_RABBITMQ = 'rabbitmq';

    public function toOptionArray()
    {
        return [
            [
                'value' => self::METHOD_CRON,
                'label' => __('Cron'),
            ],
            [
                'value' => self::METHOD_RABBITMQ,
                'label' => __('RabbitMQ'),
            ],
        ];
    }
}
