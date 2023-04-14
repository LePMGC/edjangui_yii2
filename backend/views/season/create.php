<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Season $model */

$this->title = Yii::t('app', 'Create Season');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Seasons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="season-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
