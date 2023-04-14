<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\Association;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <p>
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
            'username',
            'name',
            //'auth_key',
            //'password_hash',
            //'password_reset_token',
            'email_address:email',
            [ 
                'label' => $model->getAttributeLabel('status'),
                'value' => User::getAllStatuses()[$model->status]
            ],
            'role',
            //'created_at',
            //'updated_at',
            'phone_number',
            //'verification_code',
            'language',
            [ 
                'label' => $model->getAttributeLabel('association'),
                'value' => Association::findOne($model->association)->name,
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
