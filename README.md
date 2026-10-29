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

The module does **not** use:

```php
Magento\Framework\Registry
```

or:

```php
$registry->registry('current_category');
```

The category is obtained through:

```php
Magento\Catalog\Model\Layer\Resolver
```

This avoids the deprecated Registry-based approach and keeps the dependency explicit.

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
```

This replaces the legacy:

```text
Setup/InstallData.php
```

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

## Magento 2.4.9 / Coding Standard Changes

The original implementation was generated using an older Magento pattern. The following changes have been made for modern Magento development:

### 1. Removed Registry

Removed:

```php
Magento\Framework\Registry
```

and:

```php
$registry->registry('current_category');
```

The current category is resolved using `Magento\Catalog\Model\Layer\Resolver`.

### 2. Replaced `InstallData.php`

Removed:

```text
Setup/InstallData.php
```

and introduced a declarative Data Patch:

```text
Setup/Patch/Data/AddBottomDescriptionAttribute.php
```

### 3. Removed obsolete module setup version

The module no longer declares:

```xml
setup_version="1.0.0"
```

in `etc/module.xml`.

### 4. Removed unused Admin route

The original module did not provide an Admin controller, so the unused:

```text
etc/adminhtml/routes.xml
```

was removed.

### 5. Modern PHP

PHP files use:

```php
declare(strict_types=1);
```

Constructor property promotion and `readonly` dependencies are used where appropriate.

### 6. Explicit dependencies

The block receives its dependencies through constructor injection instead of using global state or service locator patterns.

### 7. Improved template escaping

The category attribute is intentionally rendered as HTML after Magento's category attribute processing. Therefore the template uses:

```php
/* @noEscape */
```

for the already processed category attribute output.

### 8. Removed unnecessary implementation

The original Admin route did not have a corresponding controller and was therefore unnecessary.

## Upgrade Notes

If the original module has already been installed and `bottom_description` already exists, do not manually delete the attribute.

The new Data Patch checks whether the attribute exists before creating it.

For an existing installation, run:

```bash
bin/magento setup:upgrade
bin/magento cache:clean
```

If the module was previously installed using the old `InstallData.php`, Magento's setup history may already indicate that the module has been installed. In that case, the new Data Patch must still be applied as a new patch class, which Magento will track independently.

## Development Validation

Recommended checks:

```bash
bin/magento setup:upgrade
bin/magento cache:clean
bin/magento setup:di:compile
```

For coding-standard validation:

```bash
vendor/bin/phpcs --standard=Magento2 app/code/Magelearn/CustomInfo
```

For static analysis, if PHPStan is configured in the project:

```bash
vendor/bin/phpstan analyse app/code/Magelearn/CustomInfo
```

## License

OSL-3.0
