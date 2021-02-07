<?php
/**
 * mtx_ajax_api
 * 
 * @package mtx.wp.api  
 * @author Tarchini Maurizio
 * @copyright 2017
 * @version 1.5
 * @access public
 * @since 1.0
 * @license MIT
 */
class mtx_ajax_api
{
    public $args;
    public $function;
    
        public function __construct($function,$args)
        {
         
            $this->function = $function;
            $defaults = array(  'side' => 'frontend',
                                'id_script' => null,
                                'url' => false,
                                'dep' => null,
                                'action' => '',
                                'auth' => false);
                                
            $this->args = array_merge((array)$defaults,(array)$args);
            add_action( 'wp_ajax_' . $this->args['action'], array($this, 'mtx_callback_function' ));
            if($this->args['side'] == 'frontend')
            {
                if(!$this->args['auth'])
                {
                    add_action( 'wp_ajax_nopriv_' . $this->args['action'], array($this, 'mtx_callback_function') );
                }
                
                add_action( 'wp_enqueue_scripts', array($this,'mtx_enqueue_script') );
            }
            else
            {
                add_action( 'admin_enqueue_scripts', array($this,'mtx_enqueue_script') );
            }
            
            
        }
        
        public function mtx_callback_function()
        {
            $this->mtx_verify_nonce();
            call_user_func($this->function);
            die(); 
        }
        
        public function mtx_enqueue_script()
        {
            if($this->args['url'])
            {
               wp_enqueue_script( $this->args['id_script'], get_bloginfo('template_url') . '/' . $this->args['url'], $this->args['dep'] ); 
            } 
            
            if($this->args['side'] == 'frontend')
            {
                if(!defined('FRONTEND_VAR'))
                {
                    wp_localize_script( $this->args['id_script'], 'mtx', array(
                      'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                      'nonce'     => wp_create_nonce( 'add-mtx-nonce' ))
                      );
                    define('FRONTEND_VAR', TRUE); 
                }
               
            }
            else
            {
                if(!defined('BACKEND_VAR'))
                {
                    wp_localize_script( $this->args['id_script'], 'mtx', array(
                      'nonce'     => wp_create_nonce( 'add-mtx-nonce' ))
                      );
                    define('BACKEND_VAR', TRUE); 
                }
                
            }
        }
        
        public function mtx_verify_nonce()
        {
            if ( ! wp_verify_nonce( $_REQUEST['_nonce'], 'add-mtx-nonce' ) )
            die ( 'Not allowed!');
        }
}


class mtx_ajax_api_support
{
    public function add_ajax_component($function,$args)
    {
        $$function = new mtx_ajax_api($function,$args);
    }
}