<?php

declare(strict_types=1);

namespace Magelearn\CustomInfo\Setup\Patch\Data;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddBottomDescriptionAttribute implements DataPatchInterface
{
    private const ATTRIBUTE_CODE = 'bottom_description';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly CategorySetupFactory $categorySetupFactory
    ) {
    }

    public function apply(): self
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        try {
            /** @var EavSetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create([
                'setup' => $this->moduleDataSetup,
            ]);

            if (!$categorySetup->getAttributeId(Category::ENTITY, self::ATTRIBUTE_CODE)) {
                $categorySetup->addAttribute(
                    Category::ENTITY,
                    self::ATTRIBUTE_CODE,
                    [
                        'type' => 'text',
                        'label' => 'Bottom Description',
                        'input' => 'textarea',
                        'required' => false,
                        'sort_order' => 9,
                        'wysiwyg_enabled' => true,
                        'is_html_allowed_on_front' => true,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ]
                );
            }
        } finally {
            $this->moduleDataSetup->getConnection()->endSetup();
        }

        return $this;
    }

    public function revert(): void
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        try {
            /** @var EavSetup $categorySetup */
            $categorySetup = $this->categorySetupFactory->create([
                'setup' => $this->moduleDataSetup,
            ]);

            if ($categorySetup->getAttributeId(Category::ENTITY, self::ATTRIBUTE_CODE)) {
                $categorySetup->removeAttribute(Category::ENTITY, self::ATTRIBUTE_CODE);
            }
        } finally {
            $this->moduleDataSetup->getConnection()->endSetup();
        }
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
