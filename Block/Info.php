<?php

declare(strict_types=1);

namespace Magelearn\CustomInfo\Block;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Helper\Output as CatalogOutput;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Info extends Template
{
    private ?Category $category = null;

    public function __construct(
        Context $context,
        private readonly LayerResolver $layerResolver,
        private readonly CatalogOutput $catalogOutput,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    public function getBottomDescriptionHtml(): string
    {
        $category = $this->getCurrentCategory();
        $description = (string) $category->getData('bottom_description');

        if ($description === '') {
            return '';
        }

        return (string) $this->catalogOutput->categoryAttribute(
            $category,
            $description,
            'bottom_description'
        );
    }

    /**
     * Retrieve the current category from the catalog layer.
     *
     * @throws LocalizedException If the current category cannot be resolved.
     */
    public function getCurrentCategory(): Category
    {
        if ($this->category === null) {
            $category = $this->layerResolver->get()->getCurrentCategory();

            if (!$category->getId()) {
                throw new LocalizedException(
                    __('Category object could not be found.')
                );
            }

            $this->category = $category;
        }

        return $this->category;
    }
}
