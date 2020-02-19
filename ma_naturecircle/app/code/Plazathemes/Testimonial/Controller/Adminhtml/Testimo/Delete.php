<?php
/**
* Copyright © 2015 PlazaThemes.com. All rights reserved.

* @author PlazaThemes Team <contact@plazathemes.com>
*/

namespace Plazathemes\Testimonial\Controller\Adminhtml\Testimo;

class Delete extends \Plazathemes\Testimonial\Controller\Adminhtml\Testimo
{
    public function execute()
    {
        $testimoId = $this->getRequest()->getParam('testimo_id');
        try {
            $locator = $this->_objectManager->create('Plazathemes\Testimonial\Model\Testimo')->load($testimoId);
            $locator->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
