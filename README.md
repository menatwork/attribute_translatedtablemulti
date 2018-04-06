Translated table multi
======================

The translated table multi attribute.

With this attribute you are able to create complex table structures with the MultiColumnWizard.
Create a config in the initConfig or somewhere else and write something like this:

```php
$GLOBALS['TL_CONFIG']['metamodelsattribute_multi']['mm_test']['multi_test'] = array(
    'tl_class'     => 'clr',
    'minCount'     => 0,
    'columnFields' => array(
        'col_title' => array(
            'label'     => 'Title',
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array
            (
                'style'=>'width:130px'
            )
        ),
        'col_highlight' => array(
            'label'     => 'Hervorheben',
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => array
            (
                'style' => 'width:40px'
            )
        ),
        'col_url' => array(
            'label'     => 'URL',
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array
            (
                'style'=>'width:130px', 
                'mandatory'=>false, 
                'rgxp'=>'url'
            )
        ),
    ),
);
```

The `mm_test` is the name of the table and the `multi_test` is the name of the field.
