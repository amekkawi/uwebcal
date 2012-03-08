<table <?php if (!empty($id)) echo 'id="' . $id . '"' ?> class="<?php echo Yii::app()->cssPrefix; ?>fieldtable" border="0">
	<tbody>
<?php foreach ($fields as $field) { ?>
	<tr>
		<th class="<?php echo Yii::app()->cssPrefix; ?>fieldtable-label"><?php echo CHtml::encode($field['name']); ?></th>
		<td class="<?php echo Yii::app()->cssPrefix; ?>fieldtable-value"><?php echo $field['html']; ?></td>
	</tr>
<?php } ?>
	</tbody>
</table>