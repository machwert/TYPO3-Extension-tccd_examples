<?php

# Extend site configuration by extension [E]
// Experimental example to add a new field to the site configuration

// Configure a new simple required input field to site
$GLOBALS['SiteConfiguration']['site']['columns']['siteConfigurationTestField'] = [
    'label' => 'Site configuration test field',
    'config' => [
        'type' => 'input',
    ],
];

// And add it to showitem
$GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] = str_replace(
    'base,',
    'base, siteConfigurationTestField, ',
    $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'],
);