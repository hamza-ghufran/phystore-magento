<?php
/**
* Copyright © 2015 PlazaThemes.com. All rights reserved.

* @author PlazaThemes Team <contact@plazathemes.com>
*/

namespace Plazathemes\Testimonial\Block\Adminhtml\Testimo;

use Plazathemes\Testimonial\Model\Status;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended {
	/**
	 * testimo factory
	 * @var \Plazathemes\Testimonial\Model\TestimoFactory
	 */
	protected $_testimoFactory;

	/**
	 * Registry object
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * [__construct description]
	 * @param \Magento\Backend\Block\Template\Context     $context       [description]
	 * @param \Magento\Backend\Helper\Data                $backendHelper [description]
	 * @param \Plazathemes\Testimonial\Model\TestimoFactory $testimoFactory [description]
	 * @param \Magento\Framework\Registry                 $coreRegistry  [description]
	 * @param array                                       $data          [description]
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Helper\Data $backendHelper,
		\Plazathemes\Testimonial\Model\TestimoFactory $testimoFactory,
		\Magento\Framework\Registry $coreRegistry,
		array $data = []
	) {
		$this->_testimoFactory = $testimoFactory;
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context, $backendHelper, $data);
	}

	protected function _construct() {
		parent::_construct();
		$this->setId('testimoGrid');
		$this->setDefaultSort('testimo_id');
		$this->setDefaultDir('ASC');
		$this->setSaveParametersInSession(true);
		$this->setUseAjax(true);
	}

	protected function _prepareCollection() {
		$storeViewId = $this->getRequest()->getParam('store');
		$collection = $this->_testimoFactory->create()->getCollection()->setStoreViewId($storeViewId);
		
		$collection->getSelect();
		$this->setCollection($collection);
		return parent::_prepareCollection();
	}

	/**
	 * @return $this
	 */
	protected function _prepareColumns() {
		$this->addColumn(
			'testimo_id',
			[
				'header' => __('Testimonial ID'),
				'type' => 'number',
				'index' => 'testimo_id',
				'header_css_class' => 'col-id',
				'column_css_class' => 'col-id',
			]
		);
		$this->addColumn(
			'name',
			[
				'header' => __('Name'),
				'index' => 'name',
				'class' => 'xxx',
				'width' => '50px',
			]
		);

		$this->addColumn(
			'job',
			[
				'header' => __('Job'),
				'index' => 'job',
				'class' => 'xxx',
				'width' => '50px',
			]
		);

		$this->addColumn(
			'email',
			[
				'header' => __('Email'),
				'index' => 'email',
				'class' => 'xxx',
				'width' => '50px',
			]
		);
		
		$this->addColumn(
			'avatar',
			[
				'header' => __('Avatar'),
				'class' => 'xxx',
				'width' => '50px',
				'filter' => false,
				'renderer' => 'Plazathemes\Testimonial\Block\Adminhtml\Testimo\Helper\Renderer\Image',
			]
		);
		
		$this->addColumn(
			'website',
			[
				'header' => __('Website'),
				'index' => 'website',
				'class' => 'xxx',
				'width' => '50px',
			]
		);
		
		$this->addColumn(
			'company',
			[
				'header' => __('Company'),
				'index' => 'company',
				'class' => 'xxx',
				'width' => '50px',
			]
		);
		
		$this->addColumn(
			'address',
			[
				'header' => __('Address'),
				'index' => 'address',
				'class' => 'xxx',
				'width' => '50px',
			]
		);
		
		$this->addColumn(
			'status',
			[
				'header' => __('Status'),
				'index' => 'status',
				'type' => 'options',
				'options' => Status::getAvailableStatuses(),
			]
		);
		$this->addColumn(
			'edit',
			[
				'header' => __('Edit'),
				'type' => 'action',
				'getter' => 'getId',
				'actions' => [
					[
						'caption' => __('Edit'),
						'url' => ['base' => '*/*/edit'],
						'field' => 'testimo_id',
					],
				],
				'filter' => false,
				'sortable' => false,
				'index' => 'stores',
				'header_css_class' => 'col-action',
				'column_css_class' => 'col-action',
			]
		);
		$this->addExportType('*/*/exportCsv', __('CSV'));
		$this->addExportType('*/*/exportXml', __('XML'));
		$this->addExportType('*/*/exportExcel', __('Excel'));

		return parent::_prepareColumns();
	}

	/**
	 * @return $this
	 */
	protected function _prepareMassaction() {
		$this->setMassactionIdField('testimo_id');
		$this->getMassactionBlock()->setFormFieldName('testimo');

		$this->getMassactionBlock()->addItem(
			'delete',
			[
				'label' => __('Delete'),
				'url' => $this->getUrl('testimonialadmin/*/massDelete'),
				'confirm' => __('Are you sure?'),
			]
		);

		$statuses = Status::getAvailableStatuses();

		array_unshift($statuses, ['label' => '', 'value' => '']);
		$this->getMassactionBlock()->addItem(
			'status',
			[
				'label' => __('Change status'),
				'url' => $this->getUrl('testimonialadmin/*/massStatus', ['_current' => true]),
				'additional' => [
					'visibility' => [
						'name' => 'status',
						'type' => 'select',
						'class' => 'required-entry',
						'label' => __('Status'),
						'values' => $statuses,
					],
				],
			]
		);
		return $this;
	}

	/**
	 * @return string
	 */
	public function getGridUrl() {
		return $this->getUrl('*/*/grid', ['_current' => true]);
	}
	public function getRowUrl($row) {
		return $this->getUrl(
			'*/*/edit',
			['testimo_id' => $row->getId()]
		);
	}
}
