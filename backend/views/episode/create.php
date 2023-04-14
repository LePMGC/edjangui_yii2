<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Episode $model */

$this->title = Yii::t('app', 'Create Episode');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Episodes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="episode-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
