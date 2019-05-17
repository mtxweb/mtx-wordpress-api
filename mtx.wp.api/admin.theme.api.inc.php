<?php
/**
 * adminTheme
 * 
 * @package mtx.api
 * @author Tarchini Maurizio
 * @copyright 2019
 * @version 1.0
 * @access public
 * @license MIT
 */

class adminTheme
{
	protected $prefix;
	protected $option_list = array();
	protected $title;
	protected $menu_name;
	public $opt;
	
		public function __construct($prefix, $title, $menu_name)
		{
			$this->prefix = $prefix;
			$this->title = $title;
			$this->menu_name = $menu_name;
		}
	
		public function _init()
		{
			$this->opt = get_option( $this->prefix . '_theme_options' );
			//$this->_set_default_options();
			add_action ('admin_init', array($this, '_register_options_group'));
			add_action('admin_menu', array($this, '_menu_options'));
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_required_scripts') );
		}
		
		public function enqueue_required_scripts()
		{
			wp_enqueue_style('bootstrapcss', 'https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css');
	
		}
		
		public function _set_default_options()
		{
			$options = get_option( $this->prefix . '_theme_options' );
			if($options === false)
			{
				$options = array();
			}
			foreach($this->option_list as $opt)
			{
				if(!isset($options[$opt['name']]))
				{
					$options[$opt['name']] = $opt['default'];
				}
			}
			
			update_option( $this->prefix . '_theme_options', $options );
			$this->opt = get_option( $this->prefix . '_theme_options' );
		}
	
		public function _register_options_group()
        {
           
			 register_setting($this->prefix . '_theme_options', $this->prefix . '_theme_options', array($this, 'opt_sanitize'));
			
        }
	
		public function opt_sanitize($input)
		{
			foreach($this->option_list as $option)
			{
				switch($option['typedata'])
				{
					case 'text':
					$input[$option['name']] = sanitize_text_field($input[$option['name']]);
					break;
						
					case 'textarea':
					$input[$option['name']] = sanitize_textarea_field($input[$option['name']]);
					break;
					
					case 'int':
					$input[$option['name']] = (int)$input[$option['name']];
					break;
						
					case 'float':
					$input[$option['name']] = (float)$input[$option['name']];
					break;
				}
			}
			
			return $input;
		}
		
		public function _menu_options() 
        {
            add_theme_page($this->menu_name, $this->menu_name, 'edit_theme_options', $this->prefix . '-settings', array($this,'_admin_option_page'));
        }
	
		public function add_option($name,$label,$default,$type,$data=0,$function=0,$typedata=null)
		{
			$this->option_list[] = array('name' => $name, 'label' => $label, 'default' => $default, 'type' => $type, 'data' => $data, 'function' => $function, 'typedata' => $typedata);
		}
	
		public function _admin_option_page()
        {
                 ?>
                
                <div class="wrap">
                    <div class="icon32" id="icon-options-general"><br /></div>
                    <h2><?php echo $this->title; ?></h2>
                    <p>&nbsp;</p>
					<form method="post" action="options.php">
						<div class="inside">
							<?php settings_fields($this->prefix . '_theme_options'); ?>
                        	<?php do_settings_sections($this->prefix . '-settings'); ?>
							<table class="table table-striped" id="mtx-settings-table">
                                <tbody>
									<?php
										$this->_set_default_options();
										$this->opt = get_option( $this->prefix . '_theme_options' );
										foreach($this->option_list as $option)
										{
											call_user_func_array(array($this, $option['type'] . '_option'), array($option));
										}
									?>
									<tr valign="top">
                                        <th scope="row"></th>
                                        <td>
                                            <p class="submit">
                                                <input type="submit" class="button-primary" name="submit" value="<?php _e('Save Changes') ?>" />
                                            </p>
                                        </td>
                                        <td></td>
                                    </tr>
								</tbody>
							</table>
						</div>
					</form>
				</div>
				<?php
		}
	
		public function text_option($args)
		{
			?>
									<tr>
                                        <th scope="row" style="width:20%;">
											<label for="<?php echo $args['name']; ?>">
												<?php echo $args['label']; ?>
											</label>
										</th>
                                        <td>
                                            <input type="text" name="<?php echo $this->prefix; ?>_theme_options[<?php echo $args['name']; ?>]" id="<?php echo $args['name']; ?>" class="form-control" value="<?php echo $this->opt[$args['name']]; ?>" /> 
                                        </td>
                                        <td></td>
                                    </tr>
			<?php
		}
	
		public function select_option($args)
		{
			?>
									<tr>
                                        <th scope="row" style="width:20%;">
											<label for="<?php echo $args['name']; ?>">
												<?php echo $args['label']; ?>
											</label>
										</th>
                                        <td>
                                            <select name="<?php echo $this->prefix; ?>_theme_options[<?php echo $args['name']; ?>]" id="<?php echo $args['name']; ?>" class="form-control">
												<?php
													foreach($args['data'] as $data)
													{
														?>
														<option value="<?php echo $data[1]; ?>" <?php selected($this->opt[$args['name']], $data[1]); ?>><?php echo $data[0]; ?></option>
														<?php
													}
												?>
											</select>
                                        </td>
                                        <td></td>
                                    </tr>
			<?php
		}
		public function textarea_option($args)
		{
			?>
									<tr>
                                        <th scope="row" style="width:20%;">
											<label for="<?php echo $args['name']; ?>">
												<?php echo $args['label']; ?>
											</label>
										</th>
                                        <td>
                                            <textarea name="<?php echo $this->prefix; ?>_theme_options[<?php echo $args['name']; ?>]" id="<?php echo $args['name']; ?>" class="form-control"><?php echo $this->opt[$args['name']]; ?></textarea>
                                        </td>
                                        <td></td>
                                    </tr>
			<?php
		}
		
		public function checkbox_option($args)
		{
			?>
									<tr>
                                        <th scope="row" style="width:20%;">
											<label for="<?php echo $args['name']; ?>">
												<?php echo $args['label']; ?>
											</label>
										</th>
                                        <td>
                                            <input type="checkbox" name="<?php echo $this->prefix; ?>_theme_options[<?php echo $args['name']; ?>]" id="<?php echo $args['name']; ?>" class="form-control" value='1' <?php checked($this->opt[$args['name']], '1'); ?> /> 
                                        </td>
                                        <td></td>
                                    </tr>
			<?php
		}
		
		public function radio_option($args)
		{
			?>
									<tr>
                                        <th scope="row" style="width:20%;">
											<label>
												<?php echo $args['label']; ?>
											</label>
										</th>
                                        <td>
											<?php
												foreach($args['data'] as $data)
												{
													?>
													<label for="<?php echo $args['name']; ?>">
														<?php echo $data[0]; ?>
													</label>
													<input type="radio" name="<?php echo $this->prefix; ?>_theme_options[<?php echo $args['name']; ?>]" id="<?php echo $args['name']; ?>" class="form-control" value="<?php echo $data[1]; ?>" <?php checked($this->opt[$args['name']], $data[1]); ?> />
													<?php
												}
											?>
                                             
                                        </td>
                                        <td></td>
                                    </tr>
			<?php
		}
		
		public function color_option($args)
		{
			?>
									<tr>
                                        <th scope="row" style="width:20%;">
											<label for="<?php echo $args['name']; ?>">
												<?php echo $args['label']; ?>
											</label>
										</th>
                                        <td>
                                            <input style="width: 200px;" type="color" name="<?php echo $this->prefix; ?>_theme_options[<?php echo $args['name']; ?>]" id="<?php echo $args['name']; ?>" class="form-control" value='<?php echo $this->opt[$args['name']]; ?>' /> 
                                        </td>
                                        <td></td>
                                    </tr>
			<?php
		}
		
		public function date_option($args)
		{
			?>
									<tr>
                                        <th scope="row" style="width:20%;">
											<label for="<?php echo $args['name']; ?>">
												<?php echo $args['label']; ?>
											</label>
										</th>
                                        <td>
                                            <input style="width: 200px;" type="date" name="<?php echo $this->prefix; ?>_theme_options[<?php echo $args['name']; ?>]" id="<?php echo $args['name']; ?>" class="form-control" value='<?php echo $this->opt[$args['name']]; ?>' /> 
                                        </td>
                                        <td></td>
                                    </tr>
			<?php
		}
	
		public function func_option($args)
		{
			?>
									<tr>
                                        <th scope="row" style="width:20%;">
											<label for="<?php echo $args['name']; ?>">
												<?php echo $args['label']; ?>
											</label>
										</th>
                                        <td>
                                            <?php call_user_func_array($args['function'] , array($args)); ?>
                                        </td>
                                        <td></td>
                                    </tr>
			<?php
			
		}
		
}

class admin_theme_support
{
    public function option_theme_page($prefix, $title, $menu_name)
    {
        $this->$prefix = new adminTheme($prefix, $title, $menu_name);
    }
}
?>