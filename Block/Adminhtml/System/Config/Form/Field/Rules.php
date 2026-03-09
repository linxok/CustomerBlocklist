<?php

namespace MyCompany\CustomerBlocklist\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;

class Rules extends AbstractFieldArray
{
    private ?TransferButton $transferRenderer = null;

    protected function _prepareToRender()
    {
        $this->addColumn('email', ['label' => __('Email'), 'style' => 'width: 260px;', 'size' => '40']);
        $this->addColumn('telephone', ['label' => __('Telephone'), 'style' => 'width: 180px;', 'size' => '22']);
        $this->addColumn('firstname', ['label' => __('First Name'), 'style' => 'width: 180px;', 'size' => '22']);
        $this->addColumn('lastname', ['label' => __('Last Name'), 'style' => 'width: 180px;', 'size' => '22']);
        $this->addColumn('note', ['label' => __('Note'), 'style' => 'width: 320px;', 'size' => '50']);
        $this->addColumn('transfer_action', ['label' => __('Transfer'), 'renderer' => $this->getTransferRenderer()]);
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Rule');
    }

    public function render(AbstractElement $element)
    {
        $isCheckboxRequired = $this->_isInheritCheckboxRequired($element);

        if ($element->getInherit() == 1 && $isCheckboxRequired) {
            $element->setDisabled(true);
        }

        if ($element->getIsDisableInheritance()) {
            $element->setReadonly(true);
        }

        $html = '<td class="label"></td>';
        $html .= '<td class="value">';
        $html .= '<div class="admin__field-label" style="float:none; width:auto; text-align:left; margin-bottom:8px; padding:0;">';
        $html .= '<label for="' . $element->getHtmlId() . '"><span' . $this->_renderScopeLabel($element) . '>' . $element->getLabel() . '</span></label>';
        $html .= '</div>';
        $html .= $this->_getElementHtml($element);
        $html .= '<script>require(["customerBlocklistTransferRule"]);</script>';

        if ($element->getComment()) {
            $html .= '<p class="note"><span>' . $element->getComment() . '</span></p>';
        }

        $html .= '</td>';

        if ($isCheckboxRequired) {
            $html .= $this->_renderInheritCheckbox($element);
        }

        $html .= $this->_renderHint($element);

        return $this->_decorateRowHtml($element, $html);
    }

    private function getTransferRenderer(): TransferButton
    {
        if ($this->transferRenderer === null) {
            $this->transferRenderer = $this->getLayout()->createBlock(
                TransferButton::class,
                '',
                [
                    'data' => [
                        'source_list' => $this->getSourceListType(),
                        'target_list' => $this->getTargetListType(),
                        'is_render_to_js_template' => true,
                    ],
                ]
            );
        }

        $this->transferRenderer->setData('source_list', $this->getSourceListType());
        $this->transferRenderer->setData('target_list', $this->getTargetListType());

        return $this->transferRenderer;
    }

    private function getSourceListType(): string
    {
        $element = $this->getElement();
        $name = $element ? (string)$element->getName() : '';

        return strpos($name, '[blacklist]') !== false ? 'blacklist' : 'whitelist';
    }

    private function getTargetListType(): string
    {
        return $this->getSourceListType() === 'blacklist' ? 'whitelist' : 'blacklist';
    }
}
