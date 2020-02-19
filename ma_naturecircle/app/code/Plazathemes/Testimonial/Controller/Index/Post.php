<?php
/**
* Copyright © 2015 PlazaThemes.com. All rights reserved.

* @author PlazaThemes Team <contact@plazathemes.com>
*/

namespace Plazathemes\Testimonial\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;

abstract class Post extends \Plazathemes\Testimonial\Controller\Index {
	/**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     */
    public function __construct(
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\App\Action\Context $context
    ) {
		$this->timezone = $timezone;
        parent::__construct($context);
    }
	
    /**
     * Renders CMS Home page
     *
     * @param string|null $coreRoute
     * @return \Magento\Framework\Controller\Result\Forward
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($coreRoute = null)
    {
		// $data = $this->getRequest()->getPostValue();
		// var_dump($_FILES["avatar"]);
		// var_dump($data);
	// die('fdsfds');
        
		if ($data = $this->getRequest()->getPostValue()) {
			$model = $this->_objectManager->create('Plazathemes\Testimonial\Model\Testimo');
			$storeViewId = $this->getRequest()->getParam("store");
			
			if ($id = $this->getRequest()->getParam('testimo_id')) {
				$model->load($id);
			}
			// if (in_array('0', $data['stores'])) {
				// $data['store_id'] = '0';
			// }else{
				// $data['store_id'] = implode(',',$data['stores']);
			// }
			
			/**
			 * Save image upload
			 */
			try {
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'avatar']
				);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

				/** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
				$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();

				$uploader->addValidateCallback('avatar', $imageAdapter, 'validateUploadFile');
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);

				/** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
				$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
				                       ->getDirectoryRead(DirectoryList::MEDIA);
				$result = $uploader->save($mediaDirectory->getAbsolutePath(\Plazathemes\Testimonial\Model\Testimo::BASE_MEDIA_PATH));
				$data['avatar'] = \Plazathemes\Testimonial\Model\Testimo::BASE_MEDIA_PATH . $result['file'];
			} catch (\Exception $e) {
				if ($e->getCode() == 0) {
					$this->messageManager->addError($e->getMessage());
				}
				if (isset($data['avatar']) && isset($data['avatar']['value'])) {
					if (isset($data['avatar']['delete'])) {
						$data['avatar'] = null;
						$data['delete_image'] = true;
					} else if (isset($data['avatar']['value'])) {
						$data['avatar'] = $data['avatar']['value'];
					} else {
						$data['avatar'] = null;
					}
				}
			}
			
			$now = $this->timezone->scopeTimeStamp();
			$data['created_time'] = date('Y-m-d H:i:s',$now);
			
			$model->setData($data)
			      ->setStoreViewId($storeViewId);

			try {
				$model->save();

				$this->messageManager->addSuccess(__('The testimonial has been saved.'));

				if ($this->getRequest()->getParam('back') === 'edit') {
					$this->_redirect(
						'*/*/edit',
						[
							'testimo_id' => $model->getId(),
							'_current' => true,
							'store' => $storeViewId,
							'current_slider_id' => $this->getRequest()->getParam('current_slider_id'),
							'saveandclose' => $this->getRequest()->getParam('saveandclose'),
						]
					);

					return;
				} elseif ($this->getRequest()->getParam('back') === "new") {
					$this->_redirect('*/*/new', array('_current' => true));
					return;
				}
				$this->_redirect('*/*/');
				return;
			} catch (\Magento\Framework\Model\Exception $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\RuntimeException $e) {
				$this->messageManager->addError($e->getMessage());
			} catch (\Exception $e) {
				$this->messageManager->addError($e->getMessage());
				$this->messageManager->addException($e, __('Something went wrong while saving the testimonial.'));
			}

			$this->_redirect('*/*/edit', array('testimo_id' => $this->getRequest()->getParam('testimo_id')));
			return;
		}
		$this->_redirect('*/*/');
    }
}
