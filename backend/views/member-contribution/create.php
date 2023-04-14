<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DjanguiContribution */

$this->title = Yii::t('app', 'Create Member Contribution');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Member Contributions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="djangui-contribution-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
