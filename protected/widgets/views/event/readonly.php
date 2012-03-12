<h2><?php echo CHtml::encode($event->title); ?></h2>

<p>When:<br/> <?php echo CHtml::encode($event->begintime); ?></p>

<p>Description:<br/> <?php echo CHtml::encode($event->description); ?></p>

<?php
$table = new FieldTableRenderer($this->owner, Yii::app()->eventFields, $fieldValues, $coreValues);
$table->renderReadOnly();
?>