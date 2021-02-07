<?php
require_once dirname(__FILE__) . '/../../../plugin/wp-customize-image-gallery-control/customize-image-gallery-control.php';
function classExtend()
{
	class Prefix_Separator_Control extends WP_Customize_Control {
		public function render_content() {
			?>
			<label> <br>
				<hr>
				<br> </label>
			<?php
		}
	}
	

}

add_action( 'customize_register', 'classExtend' );


class admin_theme
{
	public $wpobj;
	protected $section = array();
	protected $control = array();
	protected $color = array();
	protected $image = array();
	protected $separator = array();
	protected $cropped_image = array();
	protected $gallery = array();
		public function __construct()
		{
			global $wp_customize;
			$this->wpc = $wp_customize;
		}
		
		public function addSection($id, $title, $priority)
		{
			$this->section[] = array($id,$title,$priority);
		}
	
		public function addSeparator($id,$section,$priority)
		{
			$this->separator[] = array($id,$section,$priority);
		}
	
	
		public function addControl($id,$default,$label,$type,$section,$priority=10,$choises=false)
		{
			$this->control[] = array($id,$default,$label,$type,$section,$priority,$choises);
		}
	
		public function addColor($id,$default,$label,$section,$priority=10)
		{
			$this->color[] = array($id,$default,$label,$section,$priority);
		}
	
		public function addImage($id,$default,$label,$section,$priority=10)
		{
			$this->image[] = array($id,$default,$label,$section,$priority);
		}
	
		public function addCroppedImage($id,$default,$label,$section,$h,$w,$fh=false,$fw=false,$priority=10)
		{
			$this->cropped_image[] = array($id,$default,$label,$section,$h,$w,$fh,$fw,$priority);
		}
	
		public function addGallery($id,$label,$section,$priority)
		{
			$this->gallery[] = array($id,$label,$section,$priority);
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
			
			
			foreach($this->control as $control)
			{
					$this->wpc->add_setting($control[0], array(
						'default' => $control[1],
					) );
				
					$this->wpc->add_control( $control[0], array(
						'label'   => $control[2],
						'section' => $control[4],
						'type'    => $control[3],
						'choices' => $control[6],
						'priority'=> $control[5],
					) );
			}
			
			
			foreach($this->separator as $sep)
			{
				$this->wpc->add_setting($sep[0], array(
						'default' => $sep[0],
					) );
				$this->wpc->add_control(new Prefix_Separator_Control( $this->wpc, $sep[0], array(
					'section' => $sep[1],
					'priority' => $sep[2],
				)));
			}
			
			foreach($this->gallery as $gallery)
			{
				$this->wpc->add_setting($gallery[0], array(
						'sanitize_callback' => 'wp_parse_id_list',
					) );
				
				$this->wpc->add_control( new CustomizeImageGalleryControl\Control(
					$this->wpc,
					$gallery[0],
					array(
						'label'    => $gallery[1],
						'section'  => $gallery[2],
						'settings' => $gallery[0],
						'type'     => 'image_gallery',
						'priority' => $gallery[3],
					)
				) );
			}
			
			foreach($this->color as $color)
			{
				$this->wpc->add_setting($color[0], array(
						'default' => $color[1],
					) );
				
				$this->wpc->add_control( new WP_Customize_Color_Control( $this->wpc, $color[0], array(
					'label'   	=> $color[2],
					'section' 	=> $color[3],
					'settings'  => $color[0],
					'priority'	=> $color[4],
				) ) );
			}
			
			foreach($this->image as $image)
			{
				    $this->wpc->add_setting($image[0], array(
						'default' => $image[1],
					) );	
				
					$this->wpc->add_control( new WP_Customize_Image_Control( $this->wpc, $image[0], array(
						'label'   => $image[2],
						'section' => $image[3],
						'settings'=> $image[0],
						'priority'=> $image[4],
					) ) );
			}
			
			foreach($this->cropped_image as $image)
			{
				$this->wpc->add_setting($image[0], array(
						'default' => $image[1],
					) );
				
				$this->wpc->add_control(
					new WP_Customize_Cropped_Image_Control(
						$this->wpc,
						$image[0],
						array(
							'label'      	=> $image[2],
							'section'    	=> $image[3],
							'height'		=> $image[4],
							'width'			=> $image[5],
							'flex_width'	=> $image[6],
							'flex_height'	=> $image[7],
							'priority'		=> $image[8],
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