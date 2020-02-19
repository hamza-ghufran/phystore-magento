<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Plazathemes\Featureproductslider\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
				$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
			
				 $eavSetup->addAttribute(
						\Magento\Catalog\Model\Product::ENTITY,
						'featured',
						[
							'type' => 'int',
							'label' => 'Featured',
							'input' => 'select',
							'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
							'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
							'visible' => true,
							'required' => false,
							'user_defined' => false,
							'default' => 0,
							'searchable' => false,
							'filterable' => false,
							'comparable' => false,
							'visible_on_front' => false,
							'used_in_product_listing' => true,
							'unique' => false,
							'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_VIRTUAL, Configurable::TYPE_CODE]),
							'is_used_in_grid' => true,
							'is_visible_in_grid' => false,
							'is_filterable_in_grid' => true
						]
					);

    }
}

