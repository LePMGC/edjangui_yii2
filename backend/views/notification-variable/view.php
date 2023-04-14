<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\NotificationVariable */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notification Variables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-variable-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <!-- <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?> -->
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
            'sample_value',
            [ 
                        'label' => $model->getAttributeLabel('created_by'),
                        'value' => User::findOne($model->created_by)->name,
                    ],
                    [ 
                        'label' => $model->getAttributeLabel('created_on'),
                        'value' => Yii::$app->formatter->asDateTime($model->created_on),
                    ],
                    [ 
                        'label' => $model->getAttributeLabel('updated_by'),
                        'value' => User::findOne($model->updated_by)->name,
                    ],
                    [ 
                        'label' => $model->getAttributeLabel('updated_on'),
                        'value' => Yii::$app->formatter->asDateTime($model->updated_on),
                    ],
        ],
    ]) ?>

</div>
