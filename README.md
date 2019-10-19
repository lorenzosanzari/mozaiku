![Mozaiku logo](logo.svg)

# Mozaiku
## Plain PHP Template Inheritance

Inspired by several hierarchical template engines like Twig or Blade, 
Mozaiku offers to you the possibility of building hierarchical templates using simply **plain PHP** language.
It is a very simple and lightweight class, with very small footprint.

# License

Mozaiku is released under the [MIT](https://opensource.org/licenses/MIT) license.

# Install via Composer
If you are using [Composer](https://getcomposer.org/), you can run the following command:
```
composer require lorenzosanzari/mozaiku
```
OR you can [download](https://github.com/lorenzosanzari/mozaiku/archive/master.zip) the zip file directly from Github and extract them to your web directory.


# Template inheritance

What is template inheritance?
Template inheritance is an elegant way to make and compose reusable HTML layouts in a web application. 
It is much more powerful than traditional "include" (bad) practice (like include a header and footer file).

With Mozaiku, you can implement template inheritance simply using plain PHP language.
There is no need to learn another template language.

Having a main template, you can conceptually define a "child" view that extends the main layout.
This means that it will have the same general appearance as the layout, but will redefine some sections with its own content.
In other words, in the child view, you only need to redefine the sections you want to change with respect to the theme (section override) and the whole page will inherit all the rest of the content from the "parent" theme.

# Sections
In a layout, you can define a new section simply by writing:

```php
<?php $this->section('menu'); ?>
     Menu section content ...
<?php $this->endsection(); ?>
```

The sections can also be nested, to allow a more detailed management of the contents to be overwritten.
In the same layout it is not possible to define multiple sections with the same name.

# Parent section content
In a section it is possible to insert a new content, but also to recall the content of the same section of the parent layout, through the directive:

```php
<?php $this->section('title'); ?>

    Child title content
    <?php $this->parentContent(); //parent 'title' section content ?>

<?php $this->endsection(); ?>
```

# Extending a parent layout

Example:
```php
<!-- theme.php -->
<!DOCTYPE html>
<html>
    <head>
        <title>Mozaiku example theme</title>
    </head>
    <body>
            <?php $this->section('title'); ?>
                Theme title content
            <?php $this->endsection() ?>
            
            <div id='article'>
                <?php $this->section('article') ?>
                	This is the content of theme article body.
                <?php $this->endsection() ?>
            </div>
      </body>
</html>


<!-- page.php -->
<?php $this->extendsView('views/theme.php'); ?>
<?php $this->section('article') ?>
    	This is a modified article body overridden by page.php.
<?php $this->endsection() ?>
```

**Warning**: the $this->extendsView('my_parent_view.php') directive MUST be placed at start of the child page!

Rendering 'page.php' with Mozaiku, you will obtain the following output:

```php
<!DOCTYPE html>
<html>
    <head>
        <title>Mozaiku example theme</title>
    </head>
    <body>
            Theme title content
                       
            <div id='article'>
                This is a modified article body overridden by page.php.
            </div>
      </body>
</html>
```

If in your template you simply want to show the contents of a section 
populated by child views, you can simply write:

```php
<?php $this->showsection('my_section'); ?>
```

# Include partial view in your layout
You can include some partial view in a section of your layout (or in any point, if your view is not a child view),
simply by writing:

```php
<?php $this->includeView('views/my_partial.php', $data); ?>
```


# Rendering or Capturing Output

The render() function

```php
$mozaiku = new Mozaiku();
$data = [
	//view data array...
];
$output = $mozaiku->render('page.php', $data, $return);
```

allows you both to show the final output, and to capture its contents in a variable (string).
If third parameter (e.g $return) is equal true, $output will contain all the output from the processed view.

# Content Stacks
Mozaiku allows you to push contents to named stacks which can be rendered 
at some point in your layout. 
This is very useful to inject JavaScript or CSS tags or script required by your child views:

```php
<!-- Stack use example -->
<?php $this->push('head'); ?>
<style>
    body {font-size: 1.3em;color: #999;font-family: "Trebuchet MS",Verdana,Arial,sans-serif;}
    body a {color: #595 !important;}
    #title {font-size: 36px;}
    #menu {text-align: right; width: 100%;}
    #article, #footer {margin-top: 35px; border: 1px solid #aaa;padding: 15px 25px 15px 25px;}
    #title, #footer {text-align: center;width: 100%;}
</style>
<?php $this->endpush(); ?>
```

Render the stack:
In your html layout:

```php
<?php $this->stack('head'); ?>
```

# Debug and strict mode
You can use Mozaiku in debug mode to display debugging information 
related to the status of the internal stacks of section overrides:
```php
$mozaiku = new Mozaiku();
$mozaiku->debug = true; //debug mode
```
The strict mode will give you error messages about the optimal way to write
contents within the views:
```php
$mozaiku->strict_mode = true; //strict mode 
```
For any further doubts, see the example files included in the project. ;-)






