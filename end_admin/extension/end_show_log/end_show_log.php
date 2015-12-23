<?php
END_MODULE != 'admin' && die('Access Denied');


$logitems = end_page(model('log'),array('order'=>'`time` desc'),30);

$admins = model('admin')->get_list();
$admin = array();
foreach($admins as $_a)
{
	$admin[$_a['admin_id']] = $_a['name'];
}

?>
<style>
.log_table { border-collapse:collapse;}
.log_table td { border:1px solid #999;padding:2px;}
</style>
<table border="0" class="log_table">
<?php
foreach($logitems as $log)
{
	?>
	<tr>
		<td>
		<?php echo $admin[$log['admin_id']];?>
		</td>
		<td>
		<?php echo $log['url'];?>
		</td>
		<td>
		<?php echo date('Y-m-d H:i:s',$log['time']);?>
		</td>
	</tr>
	<?php
}
?>
</table>
<?php
echo pager_prev("上一页");
echo pager_numbers();
echo pager_next("下一页");
?>