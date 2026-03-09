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
        $markedLabel = $targetList === 'whitelist' ? __('Marked for Whitelist') : __('Marked for Blacklist');
        $inputName = (string)$this->getInputName();
        $inputId = (string)$this->getInputId();

        return '<input type="hidden" class="customerblocklist-transfer-action" '
            . 'name="' . $this->escapeHtmlAttr($inputName) . '" '
            . 'id="' . $this->escapeHtmlAttr($inputId) . '" '
            . 'value="<%- typeof transfer_action !== \'undefined\' ? transfer_action : \'\' %>" />'
            . '<button type="button" class="action-secondary customerblocklist-transfer" '
            . 'data-source-list="' . $this->escapeHtmlAttr($sourceList) . '" '
            . 'data-target-list="' . $this->escapeHtmlAttr($targetList) . '" '
            . 'data-default-label="' . $this->escapeHtmlAttr((string)$label) . '" '
            . 'data-marked-label="' . $this->escapeHtmlAttr((string)$markedLabel) . '" '
            . '><span>'
            . $this->escapeHtml((string)$label)
            . '</span></button>';
    }
}
