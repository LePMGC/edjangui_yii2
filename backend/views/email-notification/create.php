<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\EmailNotification */

$this->title = Yii::t('app', 'Create Email Notification');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Email Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-notification-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
