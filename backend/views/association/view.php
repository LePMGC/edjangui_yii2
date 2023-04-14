<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\Association;

/** @var yii\web\View $this */
/** @var common\models\Association $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Associations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="association-view">

    <p>
        <?php 
            if($model->status != Association::ASSOCIATION_ACTIVATED)
                echo Html::a(Yii::t('app', 'Activate'), ['activate', 'id' => $model->id], ['class' => 'btn btn-success']);
            else
                echo Html::a(Yii::t('app', 'Desactivate'), ['desactivate', 'id' => $model->id], ['class' => 'btn btn-warning']);
        ?>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'country',
            'city',
            'admin_phone_number',
            'admin_email_address:email',
            [ 
                'label' => $model->getAttributeLabel('status'),
                'value' => Association::getAllStatuses()[$model->status]
            ],
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
                                    ]
        ],
    ]) ?>

</div>
