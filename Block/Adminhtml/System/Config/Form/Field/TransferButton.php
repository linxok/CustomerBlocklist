<?php

namespace MyCompany\CustomerBlocklist\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\View\Element\AbstractBlock;

class TransferButton extends AbstractBlock
{
    protected function _toHtml()
    {
        $sourceList = (string)$this->getData('source_list');
        $targetList = (string)$this->getData('target_list');
        $label = $targetList === 'whitelist' ? __('Move to Whitelist') : __('Move to Blacklist');
        $moveUrl = $this->getUrl('customerblocklist/rule/move');

        return '<button type="button" class="action-secondary" '
            . 'data-move-url="' . $this->escapeHtmlAttr($moveUrl) . '" '
            . 'onclick="if(window.customerBlocklistMoveRule){window.customerBlocklistMoveRule(this,\''
            . $this->escapeJs($sourceList)
            . '\',\''
            . $this->escapeJs($targetList)
            . '\');}"><span>'
            . $this->escapeHtml((string)$label)
            . '</span></button>';
    }
}
