# Magelearn_CustomInfo

Magento 2 module that adds a store-scoped **Bottom Description** WYSIWYG attribute to categories and displays the attribute below the category product listing.

## Compatibility

- Magento Open Source / Adobe Commerce 2.4.x
- Tested design target: Magento 2.4.9
- PHP 8.1+
- Uses Magento declarative/module data patch architecture.

## Features

- Adds a `bottom_description` category EAV attribute.
- Attribute is store-scoped.
- Provides a WYSIWYG editor in the Admin category form.
- Displays the rendered content on the category view page.
- Uses dependency injection instead of Magento Registry.
- Uses a Data Patch instead of the deprecated legacy `InstallData.php` setup script.
- Does not add an unnecessary Admin route or controller.

## Installation

Copy the module to:

```text
app/code/Magelearn/CustomInfo
```

Enable the module:

```bash
bin/magento module:enable Magelearn_CustomInfo
```

Apply the data patch:

```bash
bin/magento setup:upgrade
```

Clean cache:

```bash
bin/magento cache:clean
```

For production mode:

```bash
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

## Admin Configuration

Open:

```text
Admin > Catalog > Categories
```

The category form contains:

```text
Content
└── Bottom Description
```

The field is a WYSIWYG editor and is store-scoped.

## Frontend Behavior

On a category view page, the module obtains the current category from Magento's catalog layer and renders the `bottom_description` attribute below the category product listing.

The category is obtained through:

```php
Magento\Catalog\Model\Layer\Resolver
```

## Architecture

```text
Category Admin Form
        │
        ▼
Category EAV attribute
bottom_description
        │
        ▼
Catalog Category View
        │
        ▼
Magelearn\CustomInfo\Block\Info
        │
        ├── LayerResolver
        │       └── Current Category
        │
        └── Catalog Output Helper
                └── Render category attribute
```

## Data Patch

The category attribute is created by:

```text
Setup/Patch/Data/AddBottomDescriptionAttribute.php

The patch implements:

```php
Magento\Framework\Setup\Patch\DataPatchInterface
```

The attribute is only created when it does not already exist, making the patch safe for an existing installation where the attribute has already been created.

## Category Attribute

| Property | Value |
|---|---|
| Code | `bottom_description` |
| Type | `text` |
| Input | `textarea` / WYSIWYG |
| Scope | Store View |
| Required | No |
| HTML | Allowed |
| Images | Enabled |
| Variables | Disabled |
| Widgets | Disabled |

## Files

```text
Magelearn_CustomInfo/
├── Block/
│   └── Info.php
├── Setup/
│   └── Patch/
│       └── Data/
│           └── AddBottomDescriptionAttribute.php
├── etc/
│   └── module.xml
├── view/
│   ├── adminhtml/
│   │   └── ui_component/
│   │       └── category_form.xml
│   └── frontend/
│       ├── layout/
│       │   └── catalog_category_view.xml
│       └── templates/
│           └── product/
│               └── list/
│                   └── bottom_description.phtml
├── composer.json
├── registration.php
└── README.md
```
