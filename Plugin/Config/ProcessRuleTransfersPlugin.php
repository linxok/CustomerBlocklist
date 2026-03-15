<?php

namespace MyCompany\CustomerBlocklist\Plugin\Config;

use Magento\Config\Model\Config;
use MyCompany\CustomerBlocklist\Model\RuleTransferProcessor;

class ProcessRuleTransfersPlugin
{
    private RuleTransferProcessor $ruleTransferProcessor;

    public function __construct(RuleTransferProcessor $ruleTransferProcessor)
    {
        $this->ruleTransferProcessor = $ruleTransferProcessor;
    }

    public function beforeSave(Config $subject): void
    {
        if ($subject->getSection() !== 'customerblocklist') {
            return;
        }

        $groups = $subject->getGroups();
        if (!is_array($groups)) {
            return;
        }

        $rulesGroup = $groups['rules']['fields'] ?? null;
        if (!is_array($rulesGroup)) {
            return;
        }

        $blacklist = $rulesGroup['blacklist']['value'] ?? [];
        $whitelist = $rulesGroup['whitelist']['value'] ?? [];

        if (!is_array($blacklist) || !is_array($whitelist)) {
            return;
        }

        $processed = $this->ruleTransferProcessor->process($blacklist, $whitelist);
        $groups['rules']['fields']['blacklist']['value'] = $processed['blacklist'];
        $groups['rules']['fields']['whitelist']['value'] = $processed['whitelist'];

        $subject->setGroups($groups);
    }
}
