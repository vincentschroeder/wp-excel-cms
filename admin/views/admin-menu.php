<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   WP_Excel_Cms
 * @author    Vincent Schroeder <info@webteilchen.de>
 * @license   GPL-2.0+
 * @link      http://webteilchen.de
 * @copyright 2015 Vincent Schroeder
 */
?>

<?php if (isset($createUploadFolderStarted)) { ?>
    
    <?php if($createUploadFolderRes){ ?>
        <div id="message" class="updated fade"><p>Upload folder "wp-content/uploads/wp-excel-cms" successfully created. </p></div>
    <?php }else{ ?>
        
    <?php }?>

<?php } ?>


<?php if (isset($uploadFolderDoesNotExists)) { ?>
    
<div id="message" class="error fade"><p>Can't create upload folder "wp-content/uploads/wp-excel-cms". Please create it manually and make it writable. </p></div>

<?php } ?>


<?php if(isset($deleteStarted)){ ?>
    
    <?php if($deleteResult===TRUE){ ?>
        <div id="message" class="updated fade"><p>Files are successfully deleted! </p></div>
    <?php }else{ ?>
        <div id="message" class="error fade"><p>File deletion failed, or have already been deleted before. </p></div>
    <?php  }?>

<?php  }?>



<?php if (isset($uploadStarted)) { ?>
    
    <?php if(!$uploadResult){ ?>
        <div id="message" class="error fade"><p>Upload failed! Please give it a Name and Select a File. </p></div>
    <?php }else{ ?>
        <div id="message" class="updated fade"><p>Files are successfully uploaded! </p></div>
    <?php }?>

<?php } ?>





<script>

$j=jQuery.noConflict();

$j(document).ready(function() {
    
    var uploader = $j('.uploader');
    
     $clone = uploader.clone( true );

     $j( ".edit-entry" ).bind( "click", function() {
            var slug = $j(this).attr("data-slug");
            var title = $j(this).attr("data-title");
            $clone.prependTo('#uploader-' + slug);
            $j('.input_file_name').val(title);
    });
    
    $j( ".add-new-file" ).bind( "click", function() {
         $j('#new-file-uploader').toggle();
    });
    
     
});

</script>

<div class="wrap">

	<?php screen_icon('edit-pages'); ?>
	<h2>
        <?php echo esc_html( get_admin_page_title() ); ?>
        <a href="#" class="add-new-h2 add-new-file">Create new</a>
    </h2>

    <div style="display: none;" id="new-file-uploader">
        <div id="poststuff">
            <div id="post-body" class="metabox-holder"  style="width:520px">
                <div id="post-body-content">
                    <div id="namediv" class="stuffbox">
                        <h3>Add new Excel File</h3>
                        <div class="inside">
                            <form method="post" enctype="multipart/form-data">
                                <table class="form-table" style="width:500px">
                                    <tr>
                                        <td>Name:</td>
                                        <td><input type="text" name="file_name" placeholder="Give it a name (required)"/> </td>
                                    </tr>
                                    <tr>
                                        <td>File:</td>
                                        <td><input type="file" name="file"  style="width:400px"   />Only *.xlsx Filetypes are supported</td>
                                    </tr>    
                                    <tr>
                                        <td>Upload:</td>
                                        <td><input type="submit" class="button-primary" value="Start upload" /></td>
                                    </tr>
                                </table>
                            </form>
                        </div>
                    </div>
                </div><!-- /post-body-content -->
            </div>
        </div>
    </div>  






    <div style="display: none;">
        <div class="uploader" id="uploader">
            <form method="post" enctype="multipart/form-data">
            <input class="input_file_name" type="hidden" name="file_name" /><input type="file" name="file"  /><input type="submit" class="button-primary" value="Start upload" />
            </form>
         </div>   
    </div>

    <div style="margin-top:20px;">
        <form method="post" >
            <table class="widefat">
            <thead>
                <tr>
                    <th style="width: 60px;text-align: center;">Action</th>
                    <th style="width: 60px;text-align: center;">Edit</th>
                    <th>Name</th>
                    <th>Shortcode</th>
                    <th>File Size</th>       
                     <th>Last Update</th>    
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th style="text-align: center;">Action</th>
                    <th style="text-align: center;">Edit</th>
                    <th>Name</th>
                    <th>Shortcode</th>
                    <th>File Size</th>
                    <th>Last Update</th>       
                    
                </tr>
            </tfoot>
            <tbody>
            <?php 
            if(is_array($file_data))
            foreach($file_data as $file){ 
            ?>
            
               <tr>
                 <td style="text-align: center;"><input type="checkbox" name="delete_slugs[]" value="<?php echo $file['slug'];?>" /></td>
                 <td style="text-align: center;"><a href="#" class="edit-entry" data-title="<?php echo $file['title'];?>" data-slug="<?php echo $file['slug'];?>">Edit</a></td>
                 <td class="row-title">
                    <a href="<?php echo $this->upload_base_url.'/'.$file['filename']; ?>" target="_blank"><?php echo $file['title']; ?></a>
                    <div id="uploader-<?php echo $file['slug'];?>"> </div>
                 </td>
                 <td><code>[wp_excel_cms name="<?php echo $file['title'];?>"]</code></td><!--$data = wp_excel_cms_get("<?php echo $file['title'];?>");-->
                 <td><?php echo $this->formatSizeUnits($file['filesize']); ?></td>
                 <td><?php echo date(get_option('date_format'),$file['upload_time']); ?> | <?php echo date(get_option('time_format'),$file['upload_time']); ?></td>
             </tr>
            
            <?php } ?>
            
            </tbody>
            </table>
            
            <div class="tablenav bottom">
            	<div class="alignleft actions bulkactions">
            		<select name="action2">
            			<option value="-1" selected="selected">Select action</option>
            			<option value="delete" class="hide-if-no-js">Delete files</option>
            			
            		</select>
            		<input class="button action" type="submit" name="delete_submit" value="Apply" />
            	</div>
            	<div class="alignleft actions">
            	</div>
            	<br class="clear"/>
            </div>
        
        </form>
    </div>



	<?php
		$user_info = wp_get_current_user();
		if(empty($user_info->user_firstname )){
			$show_username = ucfirst($user_info->user_login);
		}else{
			$show_username = $user_info->user_firstname;
		}
	?>

	<div class="postbox" style="width:100%;margin-top:20px;">

		<div class="inside">
			<h3>Hey <?php echo $show_username;?>!</h3>
			If you like this plugin rate it in the <a href="https://wordpress.org/support/view/plugin-reviews/wp-excel-cms" target="_blank">Plugin Directory</a>.
			If you want to improve the code or found a bug, you contribute your thoughts on <a href="https://github.com/vincentschroeder/wp-excel-cms">Github</a>.
			Don't forget to follow me on <a href="https://twitter.com/v_schroeder" target="_blank">Twitter</a>. Cheers' Vincent!
		</div>
	</div>



	<?php if(!empty($uploadResult['jsonData'])):?>
  
        <h2>Import Preview</h2>
        <div style="overflow:scroll;height:300px;width:100%;font-size:11px;color:#fff;background-color:#000;">
            <pre><?php
	            $count = 1;
	            foreach($uploadResult['jsonData'] as $data){
		           echo "################ SHEET $count #########################";
		            echo "\r\n";
		            print_r( json_decode($data));
		            $count++;
	            }
	            ?></pre>
        </div>

    <?php endif; ?>
    
    <h2>Shortcode Examples:</h2>
    <div style="background-color: #eaeaea;padding:5px;">
    <pre>
        [wp_excel_cms name="guestlist"]
        [wp_excel_cms name="guestlist" sheet="2"]
    </pre>
    </div>    
    
    
    <h2>Theme Code:</h2>
    <div style="background-color: #eaeaea;padding:5px;">
    <pre>
        //get the first sheet from guestlist.xlsx
        $data = wp_excel_cms_get("guestlist", "1");
        foreach($guestlist as $guest){
          print_r($guest);             
        }
    </pre>
    </div>
    
    
</div>
 




