<?php
/**
 * WP Excel CMS
 * 
 * @package   WP_Excel_Cms_Admin
 * @author    Vincent Schroeder
 * @license   GPL-2.0+
 * @link      http://webteilchen.de
 * @copyright 2013 Webteilchen
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @TODO: Rename this class to a proper name for your plugin.
 *
 * @package WP_Excel_Cms_Admin
 * @author  Vincent Schroeder
 */
class WP_Excel_Cms_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		/*
		 * Call $plugin_slug from public plugin class.
		 *
		 * @TODO:
		 *
		 * - Rename "Plugin_Name" to the name of your initial plugin class
		 *
		 */
		$plugin = WP_Excel_Cms::get_instance();
		$this->plugin_slug = $plugin->get_plugin_slug();

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
        add_action( 'admin_menu', array( $this, 'register_my_custom_menu_page' )  );
        
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

        
        $this->upload_dir         = wp_upload_dir();    
        $this->upload_base_url    = $this->upload_dir['baseurl'].'/wp-excel-cms';
        $this->upload_dir         = $this->upload_dir['basedir'].'/wp-excel-cms';
        $this->admin_plugin_url   = admin_url( "options-general.php?page=".$_GET["page"] );



	}


    function register_my_custom_menu_page(){
      
   
   		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'Excel Content Management System', $this->plugin_slug ),
			__( 'Excel CMS', $this->plugin_slug ),
			'edit_pages',
			$this->plugin_slug.'-admin-menu',
			array( $this, 'display_plugin_admin_menu_page' ),
            '',
            '6'
		);
   
        //add_submenu_page($this->plugin_slug.'-admin-menu', 'Neue Datei', 'Hinzuf&uuml;gen', 'edit_pages', 'my-submenu-handle', 'my_magic_function');

   
   
   
    }

################################################################################


  function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}





function create_excel_file($file_name){
   
    $new_file_name  = $file_name;
    
    if(empty($new_file_name)){
        return false;
    }
    if(empty($_FILES['file']) || empty($_FILES["file"]['name'])){
        return false;
    }
   
    require_once( plugin_dir_path( __FILE__ ) . '../includes/simplexlsx.class.php' );

	$xlsx = new SimpleXLSX( $_FILES['file']['tmp_name'] );

	$sheetNames = $xlsx->sheetNames();

	if(is_array($sheetNames)){
		foreach($sheetNames as $sheetId => $sheetName){
			if($sheetId != 1){
				$the_name = $new_file_name.'_sheet_'.$sheetId;
			}else{
				$the_name = $new_file_name;
			}

			$jsonData[$sheetId] = json_encode($xlsx->rows($sheetId));
			$this->createJsonDataFile( $jsonData[$sheetId], $the_name );

		}
	}

	$file_ext = end( explode( ".", $_FILES["file"]['name'] ) );
    
    $options = array(
        'slug'          => $new_file_name,
        'filename'      => $new_file_name.'.'.$file_ext,
        'json_file'     => $new_file_name.'.json',
        'options_file'  => $new_file_name.'.options.json',
        'file_ext'      => $file_ext,
        'filesize'      => filesize($_FILES["file"]["tmp_name"]),
        'upload_time'   => time(),
	    'sheet_names'   => $sheetNames,
    );
    
    
    
    $file_name      = $this->upload_dir.'/'.$new_file_name.'.options.json';
    $fp = fopen($file_name,"wb");
    fwrite($fp, json_encode($options));
    fclose($fp);   
    
    return array(
        'jsonData' => $jsonData,
        'options' => $options,
    );
    
}

	/**
	 * @param $jsonData
	 * @param $new_file_name
	 *
	 * @return array
	 */
	public function createJsonDataFile($jsonData, $new_file_name ) {


		$this->upload_dir = wp_upload_dir();

		#$file_name = $this->upload_dir['path']."/test.json";
		#$file_name = $uploaded_file_path_name.'.json';

		$this->upload_dir = $this->upload_dir['basedir'] . '/wp-excel-cms';
		$file_name        = $this->upload_dir . '/' . $new_file_name . '.json';


		$fp = fopen( $file_name, "wb" );
		fwrite( $fp, $jsonData );
		fclose( $fp );


		$file_ext = end( explode( ".", $_FILES["file"]['name'] ) );

		$file_name = $this->upload_dir . '/' . $new_file_name . '.' . $file_ext;
		$fp        = fopen( $file_name, "wb" );
		fwrite( $fp, file_get_contents( $_FILES["file"]["tmp_name"] ) );
		fclose( $fp );

		return true;
	}



function getFileList(){
  
    $this->upload_dir         = wp_upload_dir();    
    $this->upload_dir         = $this->upload_dir['basedir'].'/wp-excel-cms';
      
    //get all image files with a .jpg extension.
    $files = glob($this->upload_dir . "/*.options.json");
    
    for($i=0;$i<count($files);$i++){ 
        $file_data[$i] = (array) json_decode(file_get_contents($files[$i]));
    }
  
    return $file_data;  
}

 function delete_excel_files($del_files){
    
    $this->upload_dir         = wp_upload_dir();    
    $this->upload_dir         = $this->upload_dir['basedir'].'/wp-excel-cms';

    if(!is_array($del_files)){
        return false;
    }
    
    foreach($del_files as $del_file){
        
        $options_file = $this->upload_dir.'/'.$del_file.'.options.json';
        
        if(file_exists($options_file)){
            $options = (array) json_decode(file_get_contents($options_file));

	        if(isset($options['sheet_names'])){
		        foreach($options['sheetNames'] as $sheetId => $sheetName){
			        if($sheetId != 1){
			            $res    = @unlink($this->upload_dir.'/'.$options['slug'].'_sheet_'.$sheetId.'.json');
			        }
		        }
	        }

            $res    = @unlink($this->upload_dir.'/'.$options['json_file']);
            $res    = @unlink($this->upload_dir.'/'.$options['options_file']);
            $res    = @unlink($this->upload_dir.'/'.$options['filename']);
        }else{
            $res = false;
        }
        
    }
    return $res;
    
 }


################################################################################


	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), WP_Excel_Cms::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @TODO:
	 *
	 * - Rename "Plugin_Name" to the name your plugin
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), WP_Excel_Cms::VERSION );
		}

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    1.0.0
	 */
	public function add_plugin_admin_menu() {

		/*
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * @TODO:
		 *
		 * - Change 'Page Title' to the title of your plugin admin page
		 * - Change 'Menu Text' to the text for menu item for the plugin settings page
		 * - Change 'manage_options' to the capability you see fit
		 *   For reference: http://codex.wordpress.org/Roles_and_Capabilities
		 */
		$this->plugin_screen_hook_suffix = add_options_page(
			__( 'WP Excel CMS Settings', $this->plugin_slug ),
			__( 'WP Excel CMS', $this->plugin_slug ),
			'manage_options',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}
    
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_admin_menu_page() {
	   
       
       
        //plugins_url( 'images/wordpress.png' , __FILE__ )
        
        $this->upload_dir         = wp_upload_dir();    
        $this->upload_base_url    = $this->upload_dir['baseurl'].'/wp-excel-cms';
        $this->upload_dir         = $this->upload_dir['basedir'].'/wp-excel-cms';
        $this->admin_plugin_url   = admin_url( "options-general.php?page=".$_GET["page"] );
       
       
        if(!is_dir($this->upload_dir)){
            $createUploadFolderStarted  = true;
            $createUploadFolderRes      = mkdir($this->upload_dir);
        }
        if(!is_dir($this->upload_dir)){
            $uploadFolderDoesNotExists = true;
        }
       
        
        if($_POST['action2']=='delete'){
            $deleteStarted = true;
            $deleteResult = $this->delete_excel_files($_POST['delete_slugs']);
           
        }       
       
       
       
        if (isset($_FILES['file'])) {
            $uploadStarted  = true;
            $uploadResult     = $this->create_excel_file($_POST['file_name']); 
        }
       
       
       
       
        $file_data = $this->getFileList();
         
         
         
       
		include_once( 'views/admin-menu.php' );
	}




	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}



}
