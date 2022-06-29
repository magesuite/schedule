<?php
namespace MageSuite\Schedule\Helper;

class Configuration
{
    const CRON_GROUP_ID = 'scheduler';
    const CRON_JOB_METHOD_FORMAT = 'schedule_scheduler_%s_%s';

    const XML_PATH_IS_ENABLED = 'schedule/general/is_enabled';
    const XML_PATH_SCHEDULING_METHOD = 'schedule/general/method';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_IS_ENABLED);
    }

    public function getSchedulingMethod()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SCHEDULING_METHOD);
    }
}
