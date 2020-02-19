<?php
/**
* Copyright © 2015 PlazaThemes.com. All rights reserved.

* @author PlazaThemes Team <contact@plazathemes.com>
*/

namespace Plazathemes\Testimonial\Controller\Adminhtml\Testimo;

class MassStatus extends \Plazathemes\Testimonial\Controller\Adminhtml\Testimo
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $testimoIds = $this->getRequest()->getParam('testimo');
        $status = $this->getRequest()->getParam('status');
        $storeViewId = $this->getRequest()->getParam('store');
        var_dump($status);
        // die;
        if (!is_array($testimoIds) || empty($testimoIds)) {
            $this->messageManager->addError(__('Please select testimonial(s).'));
        } else {
            try {
                foreach ($testimoIds as $testimoId) {
                    // $testimo = $this->_testimoFactory->create()->setStoreViewId($storeViewId)->load($testimoId);
                    $testimo = $this->_objectManager->create('Plazathemes\Testimonial\Model\Testimo')->load($testimoId);
                    $testimo->setStatus($status)
                           ->setIsMassupdate(true)
                           ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been changed status.', count($testimoIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/', ['store' => $this->getRequest()->getParam("store")]);
    }
}
