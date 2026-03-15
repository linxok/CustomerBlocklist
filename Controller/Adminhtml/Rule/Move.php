<?php
namespace MyCompany\CustomerBlocklist\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use MyCompany\CustomerBlocklist\Model\MoveRuleBetweenLists;

class Move extends Action
{
    public const ADMIN_RESOURCE = 'MyCompany_CustomerBlocklist::config';

    private MoveRuleBetweenLists $moveRuleBetweenLists;

    public function __construct(Context $context, MoveRuleBetweenLists $moveRuleBetweenLists)
    {
        parent::__construct($context);
        $this->moveRuleBetweenLists = $moveRuleBetweenLists;
    }

    public function execute()
    {
        $sourceList = (string)$this->getRequest()->getParam('source_list');
        $targetList = (string)$this->getRequest()->getParam('target_list');
        $backUrl = (string)$this->getRequest()->getParam('back_url');
        $store = (string)$this->getRequest()->getParam('store');
        $website = (string)$this->getRequest()->getParam('website');
        
        $scope = 'default';
        $scopeId = 0;
        
        if ($store !== '') {
            $scope = 'stores';
            $scopeId = (int)$store;
        } elseif ($website !== '') {
            $scope = 'websites';
            $scopeId = (int)$website;
        }
        
        $rule = [
            'email' => (string)$this->getRequest()->getParam('email'),
            'telephone' => (string)$this->getRequest()->getParam('telephone'),
            'firstname' => (string)$this->getRequest()->getParam('firstname'),
            'lastname' => (string)$this->getRequest()->getParam('lastname'),
            'note' => (string)$this->getRequest()->getParam('note'),
        ];

        try {
            $addedToTarget = $this->moveRuleBetweenLists->execute($sourceList, $targetList, $rule, $scope, $scopeId);
            if ($addedToTarget) {
                $this->messageManager->addSuccessMessage(__('The rule was moved to %1.', ucfirst($targetList)));
            } else {
                $this->messageManager->addNoticeMessage(__('The rule was removed from %1 because it already exists in %2.', ucfirst($sourceList), ucfirst($targetList)));
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Unable to move the rule.'));
        }

        if ($backUrl !== '') {
            return $this->_redirect($backUrl);
        }

        return $this->_redirect('adminhtml/system_config/edit/section/customerblocklist');
    }
}
