<?php
/**
 * custom_box_api
 * 
 * @package mtx.wp.api   
 * @author Tarchini Maurizio
 * @copyright 2017
 * @version 1.3
 * @access public
 * @since 1.0
 * @license MIT
 */
class custom_box_api
{
    public $fields = array();
    public $id;
    public $title;
    public $type;
    public $position;
    
        public function __construct($id,$title,$type,$position = 'side')
        {
            $this->id = $id;
            $this->title = $title;
            $this->type = $type;
            $this->position = $position;
        }
        
        public function add_field($type,$name,$label,$clean = false,$items = array())
        {
            $i = count($this->fields);
            $this->fields[$i]['type'] = $type; // text - select - textarea - ckb
            $this->fields[$i]['name'] = $name;
            $this->fields[$i]['label'] = $label;
            $this->fields[$i]['clean'] = $clean; //url - html - textarea - attr - int
            $this->fields[$i]['items'] = $items;
        }
        
        public function make_fileds($post)
        {
            foreach($this->fields as $field)
            {
                switch($field['type'])
                {
                    case 'text':
                    ?>
                        <p><label for="<?php echo $field['name']; ?>"><?php echo $field['label']; ?></label>
                        <input style="float: right;" type="text" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" value="<?php echo get_post_meta($post->ID, $field['name'], true); ?>"/></p>
                    <?php
                    break;
                    
                    case 'select':
                    ?>
                        <p>
                            <label for="<?php echo $field['name']; ?>"><?php echo $field['label']; ?></label>
                            <select style="float: right;" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>">
                                <?php
                                    foreach($field['items'] as $item)
                                    {
                                        ?>
                                            <option value="<?php echo $item['value']; ?>" <?php selected(get_post_meta($post->ID,$field['name'], true), $item['value'] ); ?>><?php echo $item['label']; ?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                        </p>
                    <?php
                    break;
                    
                    case 'textarea':
                    ?>
                        <p style="height: 60px;">
                            <label for="<?php echo $field['name']; ?>"><?php echo $field['label']; ?></label>
                            <textarea style="float: right" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>"><?php echo get_post_meta($post->ID, $field['name'], true); ?></textarea>
                        </p>
                    <?php
                    break;
                    
                    case 'ckb':
                    ?>
                        <p>
                            <label for="<?php echo $field['name']; ?>"><?php echo $field['label']; ?></label>
                            <input style="float: right;" type="checkbox" value="1" id="<?php echo $field['name']; ?>" name="<?php echo $field['name']; ?>" <?php checked(get_post_meta($post->ID,$field['name'],true),'1'); ?> />
                        </p>   
                    <?php
                    break;
                }
                
            }
        }
        
        public function define_metabox()
        {
            global $post;
            add_meta_box($this->id,$this->title,array($this,'make_fileds'),$this->type,$this->position);
        }
        
        public function do_metabox()
        {
            add_action( 'add_meta_boxes', array($this,'define_metabox'));
            add_action('save_post', array($this,'save_metabox_data'));
        }
        
        public function save_metabox_data()
        {
            
            global $post;
            if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            {
              return;  
            }
            
            if(!isset($_POST['post_ID']) || !isset($_POST['post_type']))
            {
                return;
            }
            
            if(trim($_POST['post_type']) != trim($this->type))
            {
                return;
            }
            
            if(isset($_POST[$this->fields[0]['name']]))
            {
                foreach($this->fields as $field)
                {
                   if($field['clean'])
                   {
                        switch($field['clean'])
                        {
                            case 'url':
                            $content = esc_url($_POST[$field['name']]);
                            break;
                            
                            case 'html':
                            $content = esc_html($_POST[$field['name']]);
                            break;
                            
                            case 'textarea':
                            $content = esc_textarea($_POST[$field['name']]);
                            break;
                            
                            case 'attr':
                            $content = esc_attr($_POST[$field['name']]);
                            break;
                            
                            case 'int':
                            $content = (int)$_POST[$field['name']];
                            break;
                        }
                   }
                   else
                   {
                        $content = $_POST[$field['name']];
                   }
                   
                   update_post_meta($post->ID, $field['name'], $content); 
                }
            }
        }
}


class metabox_support
{
    public function new_custombox($id,$title,$type,$position = 'side')
    {
        $this->$id = new custom_box_api($id,$title,$type,$position ='side');
    }
}

?>