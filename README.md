MTX WORDPRESS API
======

A small collection of powerful API for wordpress developers.

##Installation and use

###1 Include mtx.load.api.php

Include mtx.load.api.php in wp functions.php

```bash
require_once 'path_to/mtx.load.api.php';
```

###2 use $mtx istance

####2.1 Creating metaboxes

The first step is to create the structure of the metabox

```bash
$mtx->custombox->new_custombox('test','My test metabox','post');
```  
The first argument specifies the id of the metabox. They do not allow symbols and spaces. It must be in a valid format for a php variable.
The second argument is the title.
The third argument is the custom post type where will you appear to custombox.

You can now add fields. Attention, the right metaboxes be specified as follows

```bash  
$mtx->custombox->[metabox id]->add_field(.....);
```

Add simple text field

```bash  
$mtx->custombox->test->add_field('text', 'name', 'your name', 'attr');
```
The first argument is the field type (text, textarea, ckb (for checkbox) and select).
The second argument is the name of the field that will also be used for saving in the database.
The third argument is the field label.
The fourth argument is escape type (url - html - textarea - attr - int)

You can add as many desires fields.

To select the type fields the syntax is particularly:
```bash
$mtx->custombox->test->add_field('select', 'myselect', 'select value', 'attr', array(array('value' => '0', 'label' => 'value 0'),
                                                                                     array('value' => '1', 'label' => 'value 1'),
                                                                                     array('value' => '2', 'label' => 'value 2')));
```

In the end we create Metaboxes

```bash  
$mtx->custombox->test->do_metabox();
```


####2.2 Register ajax component

Easily add an ajax component:

```bash
$mtx->ajax->add_ajax_component('my_function', $args);
```

The first argument is the callback function.
The second argument is an array with the following parameters:

- side: frontend or backend - It defines whether the call is made in the administrative side or the public side (default frontend)
- auth: if defined true, ajax calls on the public side can take place only by authenticated users (default false)
- action: The action value to pass in the ajax call (default '')
- id_script, url, dep: If the js code required is not already included it is possible to do with these three parameters. It works as wp_enqueue_script (id script, the script location (starting from the theme directory), an array with dependencies) (deafult null, null, null).

example of ajax call

```bash
$.post(mtx.ajaxurl, {action:'my_action',_nonce:mtx.nonce, ......)
```

example of ajax call (admin side)

```bash
$.post(ajaxurl, {action:'my_action',_nonce:mtx.nonce, ......)
```

####2.3 Create an options page for a theme

First declare a new options page

```bash
$mtx->admin->option_theme_page($id, $title, $menu_item);
```
The first argument specifies the id of option page and prefix of option group. They do not allow symbols and spaces. It must be in a valid format for a php variable. The options array will be saved as **[$id]_theme_options**
The second argument is the title of the option page.
The third argument is the menu item text.

You can now add fields.

```bash
$mtx->admin->[$id]->add_option(....);
```

```bash
$mtx->admin->option_theme_page('test', 'My options page', 'theme options');
$mtx->admin->test->add_option($name,$label, $default,$type,$data,$funtion,$sanitize);
```

$name: option name
$label: field label
$default: default value
$type: type of field (allowed: text, textarea, select, checkbox, radio, color, date, func)
$data: If it is a select or a radio group -> array(array(key,value),array(key,value).....)
$function: If type is func, callable function
$sanitize: sanitize type (allowed: text, textarea, int, float)

```bash
$mtx->admin->option_theme_page('test', 'My options page', 'theme options');
$mtx->admin->test->add_option('name','your name, 'default value, 'text, null, null, 'text');
$mtx->admin->test->add_option('gender','Gender', 'f','select', array(array('male', 'm'), array('female', 'f')));
$mtx->admin->test->_init();
```

**note that the declaration must end with the _init () method**

####2.4 Activate db tools

db tools contains two small utility. To activate it is enough to write

```bash
$mtx->db->mtx_db_support();
```

Now in the admin panel you will find a new menu called mtx db tools. In this menu we have two buttons:

1. database maintenance. Delete posts with status "autodraft" and "trash". After that performs an optimization of the database tables
2. database backup. It performs a database dump and saves it in the folder mtx.wp.api with the name  db_backup.sql. It is not a good idea to leave it in that position ...

####2.5 Activate inclusion tools

mtx.wp.api has two utilities to automate the inclusion

```bash
$mtx->load->inc();
```

If you activated, includes all files in the folder /{themefolder}/inc/ that have the following format

```bash
file-name.inc.php
```

```bash
$mtx->load->widget();
```

If activated, records and activates all custom widgets in the /{themefolder}/widget/ folder that have the following format

```bash
{class-name-of-widget}.widget.php

(class class-name-of-widget extends WP_Widget)
```