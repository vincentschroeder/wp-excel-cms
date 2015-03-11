

<table class="wp_excel_cms_table wp_excel_cms_table_<?php echo $name; ?>">

<?php $i = 0; ?>

<?php foreach($data as $entry): ?>

    <?php if($i==0){$count = count($entry);} ?> 
    
    <tr>
     <?php if($i==0):  ?> 
        
        <?php foreach ($entry as $cell): ?>
            <th><?php echo $cell; ?></th>
        <?php endforeach; ?> 
     
     <?php else: ?>

        <?php foreach ($entry as $cell): ?>
            <td><?php echo $cell; ?></td>
        <?php endforeach; ?>     
     
     <?php endif; ?>
    </tr>

    
    <?php $i++; ?>
    
<?php endforeach; ?>
</table>