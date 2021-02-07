<?php
/**
 * include_api
 * 
 * @package mtx.wp.api  
 * @author Tarchini Maurizio
 * @copyright 2017
 * @version 1.3
 * @access public
 * @since 1.0
 * @license MIT
 */
class include_api
{
    
    public function widget()
    {
        $mtx_widgets = glob(get_theme_root() . '/' . get_template() . '/widget/*.widget.php');
        
        if($mtx_widgets)
        {
            foreach($mtx_widgets as $widget)
            {
                require_once $widget;
                $class_name = '';
                
                $part = explode('/', $widget);
                $name = end($part);
                $complete_name = explode('.',(string)$name );
                $class_name = reset($complete_name);
                $widget_init_args[] = $class_name;
            }
            foreach($widget_init_args as $arg)
            {
                add_action('widgets_init', function() use ($arg) {return register_widget($arg);});
            }
        }
    }
    
    
    public function inc()
    {
        $mtx_functions = glob(get_theme_root() . '/' . get_template() . '/inc/*.inc.php');
        if($mtx_functions)
        {
            foreach($mtx_functions as $file)
            {
                if(strpos($file,'mtxcommentform') AND is_admin())
                {
                    continue;
                }
                require_once $file;
            }   
        }
    }
}