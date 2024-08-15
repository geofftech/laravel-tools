# GeoffTech - Laravel Tools

Various useful features and functions

## HasStorage Trait

- if a model has links to files in storage, this trait will ensure those files are removed when they are de-linked from the model.
- works with Soft Deletes.
- On update, removed any files no longer linked to the model.
- On delete, removes all linked files.

```
    use HasStorage;

    protected $storage = [
        'image',
        'banner',
        'content' => 'image',
    ];
```

For simple fields

- just pass the field name

For json fields

- we will extract out all properties in the JSON that match the names passed.
- this can be a comma delimited string, an array, or a function that returns an array.

## ArrayHelper

### toSnakeCase

- convert each key to snake case version.
- maps overrides.

## HtmlHelper

### hasText

- strips tags and white space to see if there is any text present.
