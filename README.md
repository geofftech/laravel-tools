# GeoffTech - Laravel Tools

Various useful features and functions

## Agent Instructions

This is a Laravel package providing utility traits and helper classes. When working with this package:

### Package Structure

- **Traits**: Located in `src/Traits/` - reusable functionality for Laravel models
- **Helpers**: Located in `src/Helpers/` - utility classes for common operations
- **Controllers**: Located in `src/Controllers/` - base controller functionality
- **Config**: Package configuration in `config/tools.php`
- **Service Provider**: Main package registration in `src/ServiceProvider.php`

### Development Guidelines

- Follow PSR-4 autoloading standards with namespace `GeoffTech\LaravelTools`
- Use Laravel conventions for naming and structure
- Include comprehensive docblocks for all public methods
- Write tests for new functionality
- Maintain backward compatibility when possible
- Use semantic versioning for releases

### Key Features

- **HasStorage Trait**: Automatic file cleanup for model storage fields
- **ArrayHelper**: Utility methods for array manipulation
- **HtmlHelper**: HTML content processing utilities

### Testing

Run tests with: `composer test` or `./vendor/bin/phpunit`

### Code Style

Use Laravel Pint for code formatting: `./vendor/bin/pint`

## HasStorage Trait

- if a model has links to files in storage, this trait will ensure those files are removed when they are de-linked from the model.
- works with Soft Deletes.
- On update, removed any files no longer linked to the model.
- On delete, removes all linked files.

```php
    use HasStorage;

    protected $storage = [
        'image',
        'banner',
        'content' => 'image',
        'content' => 'image,file',
        'content' => ['image', 'file'],
    ];
```

For `simple fields`

- just pass the field name

For `JSON fields`

- we will extract out all properties in the JSON that match the property names passed.
- this can be a comma delimited string, an array, or a function that returns an array.

This assumes the `public` disk. To set the disk

```php
    protected $storage_disk = 'private';
```

or to set per field

```php
    protected $storage_disk = [
        'content' => 'private'
        ];
```

## ArrayHelper

### toSnakeCase

- convert each key to snake case version.
- maps overrides.

## HtmlHelper

### hasText

- strips tags and white space to see if there is any text present.
