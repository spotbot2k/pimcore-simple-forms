# Advanced Example

## Complex data

Some of the data in the form may not be transformed into the plain text for an email. The files for example will remain in form of Pimcore assets and you will need to loop over them in your twig templates or listeners and generate the readable form yourself. The same goes for multiple choice, provided it is not in form of radio input.

The simplest way is just to use the `join` filter. For example you have an upload field named "files", then

``` twig
{{ files | join(', ') }}
```

will be replaces with paths of the uploaded assets separated by comma.

## Further Topics

- [How to Override any Part of a Bundle](https://symfony.com/doc/current/bundles/override.html)
- [How to Customize Form Rendering](https://symfony.com/doc/current/form/form_customization.html)