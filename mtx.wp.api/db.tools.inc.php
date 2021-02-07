<?php
/**
 * mtx_db_tools
 * @package mtx.wp.api  
 * @author Tarchini Maurizio
 * @copyright 2017
 * @version 1.8.1
 * @since 1.5
 * @access public
 * @license MIT
 */
class mtx_db_tools
{
    protected $wpdb_;
    
        public function __construct()
        {
            global $wpdb;
            $this->wpdb_ = $wpdb;
            add_action( 'admin_menu', array($this, 'db_menu_page' ));
        }
        
        public function db_menu_page()
        {
            add_menu_page('MTX DB TOOLS', 'MTX DB TOOLS', 'manage_options', 'mtx-db-tools', array($this, 'admin_db_menu_page' ), 'dashicons-admin-tools');
        }
        
        public function admin_db_menu_page()
        {
            if(isset($_GET['cdb']))
            {
                $this->deleteDraftPost();
                $this->deleteTrashPost();
                $this->optimizeTables();
                echo '<div id="Scmessage" class="updated notice notice-success is-dismissible below-h2"><p>All posts with status "autodraft" and "trash" were deleted. The database tables have been optimized.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">hide</span></button></div>';

            }
            if(isset($_GET['bkdb']))
            {
                $this->createBackup();
                echo '<div id="Scmessage" class="updated notice notice-success is-dismissible below-h2"><p>The database dump was saved in the folder mtx.wp.api</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">hide</span></button></div>';

            }
            
            echo '<h1>MTX DB TOOLS</h1><br /><hr /><br />';
            echo '<form method="post" action="' . $_SERVER['REQUEST_URI'] . '&amp;cdb=1"><button type="submit" class="button button-primary button-large">&nbsp;&nbsp;database maintenance &nbsp;&nbsp;</button></form><br /><br />';
            echo '<form method="post" action="' . $_SERVER['REQUEST_URI'] . '&amp;bkdb=1"><button type="submit" class="button button-primary button-large">database backup</button></form>';
        }
        protected function deleteDraftPost()
        {
            $sql = "DELETE FROM " . $this->wpdb_->prefix . "posts WHERE post_status='auto-draft'";
            $this->wpdb_->query($sql);
        }
        
        protected function deleteTrashPost()
        {
            $sql = "DELETE FROM " . $this->wpdb_->prefix . "posts WHERE post_status='trash'";
            $this->wpdb_->query($sql);
        }
        
        protected function optimizeTables()
        {
            $sql = "SHOW TABLES";
            $res = $this->wpdb_->get_col($sql);
            
            foreach($res as $row)
            {
                $query = "OPTIMIZE TABLE " . $row;
                $this->wpdb_->query($query);
            }
        }
        
        protected function createBackup()
        {

            $tables = array();
            $return = '';
            $mysqli = mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
            $result = mysqli_query($mysqli, 'SHOW TABLES');
            while($row = mysqli_fetch_row($result))
            {
              $tables[] = $row[0];
            }
          
          foreach($tables as $table)
          {
            $result = mysqli_query($mysqli, 'SELECT * FROM '.$table);
            $num_fields = mysqli_num_fields($result);
            
            $row2 = mysqli_fetch_row(mysqli_query($mysqli, 'SHOW CREATE TABLE '.$table));
            $return.= "\n\n".$row2[1].";\n\n";
            
            for ($i = 0; $i < $num_fields; $i++) 
            {
              while($row = mysqli_fetch_row($result))
              {
                $return.= 'INSERT INTO '.$table.' VALUES(';
                for($j=0; $j<$num_fields; $j++) 
                {
                  $row[$j] = addslashes($row[$j]);
                  $row[$j] = str_replace("\n","\\n",$row[$j]);
                  if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                  if ($j<($num_fields-1)) { $return.= ','; }
                }
                $return.= ");\n";
              }
            }
            $return.="\n\n\n";
          }
          
          $handle = fopen(dirname(__FILE__) . '/db-backup.sql','w+');
          fwrite($handle,$return);
          fclose($handle);
        
        }
}

class db_tools_support
{
    public function mtx_db_support()
    {
        $obj = new mtx_db_tools();
    }
}