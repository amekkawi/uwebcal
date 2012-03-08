<table <?php if (!empty($id)) echo 'id="' . $id . '"' ?> class="<?php echo Yii::app()->cssPrefix; ?>fields-fieldtable" border="0">
	<tbody>
<?php foreach ($fields as $field) { ?>
	<tr>
		<th class="<?php echo Yii::app()->cssPrefix; ?>fields-fieldtable-label"><?php echo CHtml::encode($field['name']); ?></th>
		<td class="<?php echo Yii::app()->cssPrefix; ?>fields-fieldtable-value"><?php echo $field['html']; ?></td>
	</tr>
<?php } ?>
	</tbody>
</table>