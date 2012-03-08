<div <?php if (!empty($id)) echo 'id="' . $id . '"' ?> class="<?php echo Yii::app()->cssPrefix; ?>fields-standalonefield">
	<div class="<?php echo Yii::app()->cssPrefix; ?>fields-standalonefield-label"><?php echo CHtml::encode($fields[0]['name']); ?></div>
	<div class="<?php echo Yii::app()->cssPrefix; ?>fields-standalonefield-value"><?php echo $fields[0]['html']; ?></div>
</div>
