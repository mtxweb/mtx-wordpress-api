<?php
class admin_theme
{
	public $wpobj;
	protected $section = array();
	protected $control = array();
	protected $setting = array();
	protected $color = array();
	protected $image = array();
	protected $cropped_image = array();
		public function __construct()
		{
			global $wp_customize;
			$this->wpc = $wp_customize;
		}
		
		public function addSection($id, $title, $priority)
		{
			$this->section[] = array($id,$title,$priority);
		}
	
		public function addControl($id,$label,$type,$section,$choises=false)
		{
			$this->control[] = array($id,$label,$type,$section,$choises);
		}
	
		public function addColor($id,$label,$section)
		{
			$this->color[] = array($id,$label,$section);
		}
	
		public function addImage($id,$label,$section)
		{
			$this->image[] = array($id,$label,$section);
		}
	
		public function addCroppedImage($id,$label,$section,$h,$w,$fh=false,$fw=false)
		{
			$this->cropped_image[] = array($id,$label,$section,$h,$w,$fh,$fw);
		}
	
		public function addSetting($id,$default)
		{
			$this->setting[] = array($id,$default);
		}
	
		public function createMenu()
		{
			foreach($this->section as $section)
			{
				$this->wpc->add_section( $section[0], array(
					'title'          => $section[1],
					'priority'       => $section[2],
				) );
			}
			
			foreach($this->setting as $setting)
			{
				$this->wpc->add_setting( $setting[0], array(
					'default'        => $setting[1],
				) );
			}
			
			foreach($this->control as $control)
			{
					$this->wpc->add_control( $control[0], array(
						'label'   => $control[1],
						'section' => $control[3],
						'type'    => $control[2],
						'choices' => $control[4],
					) );
			}
			
			foreach($this->color as $color)
			{
				$this->wpc->add_control( new WP_Customize_Color_Control( $this->wpc, $color[0], array(
					'label'   => $color[1],
					'section' => $color[2],
					'settings'   => $color[0],
				) ) );
			}
			
			foreach($this->image as $image)
			{
				    $this->wpc->add_control( new WP_Customize_Image_Control( $this->wpc, $image[0], array(
						'label'   => $image[1],
						'section' => $image[2],
						'settings'   => $image[0],
					) ) );
			}
			
			foreach($this->cropped_image as $image)
			{
				$this->wpc->add_control(
					new WP_Customize_Cropped_Image_Control(
						$this->wpc,
						$image[0],
						array(
							'label'      	=> $image[1],
							'section'    	=> $image[2],
							'height'		=> $image[3],
							'width'			=> $image[4],
							'flex_width'	=> $image[5],
							'flex_height'	=> $image[6],
						)
					)
				);
			}
		}
	
		public function doAdmin()
		{
			add_action( 'customize_register', array($this,'createMenu') );
		}
	
		
}


?>