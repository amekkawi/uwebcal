<?php
$this->pushPageTitleSegment(Yii::t('headers', 'Event: {title}', array('{title}'=>$event->title)));
$this->widget('EventWidget', array('event'=>$event));
?>
