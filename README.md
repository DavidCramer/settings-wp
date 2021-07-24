# Settings-wp

Simple settings management for WordPress

### Using

The helper function makes it easy to register a collection in a more standard way.
```php
// Register a new collection.
$setting = SettingsWP( 'media_settings' );

// Add settings and set defaults. 
$setting->add( 'image.quality', 'auto' );
$setting->add( 'image.breakpoints.max_number', 10 );
$setting->add( 'image.breakpoints.max_width', 2500 );
$setting->add( 'image.format', 'auto' );
$setting->add( 'video.quality', 'auto' );
$setting->add( 'video.format', 'auto' );
$setting->add( 'video.compression', '50%' );
```
This sets up the defaults and structures your setting collection neatly.
```php
// Retrieving the root level. 
var_export( $setting->get() );
array (
  'image' => 
  array (
    'breakpoints' => 
    array (
      'max_number' => 10,
      'max_width' => 2500,
    ),
    'format' => 'auto',
    'quality' => 'auto',
  ),
  'video' => 
  array (
    'compression' => '50%',
    'format' => 'auto',
    'quality' => 'auto',
  ),
)
```
You can also retrieve values at partial levels.
```php
var_export( $setting->get('image') );
array (
  'breakpoints' => 
  array (
    'max_number' => 10,
    'max_width' => 2500,
  ),
  'format' => 'auto',
  'quality' => 'auto',
);

var_export( $setting->get('image.breakpoints') );
array (
  'max_number' => 10,
  'max_width' => 2500,
);

var_export( $setting->get('video.compression') );
'50%'
```
You can also register settings and set defaults in a chained parameter way.
```php
// Register a new collection.
$setting                                 = SettingsWP( 'media_settings' );
// Set parameters.
$setting->images->news                   = 'asd';
$setting->image->quality                 = 'auto';
$setting->image->breakpoints->max_number = 10;
$setting->image->breakpoints->max_width  = 1500;
$setting->image->format                  = 'auto';
$setting->video->quality                 = '80%';
$setting->video->format                  = 'mp4';
$setting->video->compression             = '50%';

var_export( $setting->get() );
array (
  'image' => 
  array (
    'breakpoints' => 
    array (
      'max_number' => 10,
      'max_width' => 1500,
    ),
    'format' => 'auto',
    'quality' => 'auto',
  ),
  'images' => 
  array (
    'news' => 'asd',
  ),
  'video' => 
  array (
    'compression' => '50%',
    'format' => 'mp4',
    'quality' => '80%',
  ),
)

// To get a value along the chain.
var_export( $setting->image->breakpoints->get() );
array (
  'max_number' => 10,
  'max_width' => 1500,
)
// To get a last item.
var_export( $setting->image->breakpoints->max_number->get() );
10
// Or to use it as a variable, add an _ to the last parameter.
var_export( $setting->image->breakpoints->_max_width) );
2500
```
