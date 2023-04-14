<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\SmsNotification */

$this->title = Yii::t('app', 'Create Sms Notification');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sms Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-notification-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
