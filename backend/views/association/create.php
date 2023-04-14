<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Association $model */

$this->title = Yii::t('app', 'Create Association');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Associations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="association-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
