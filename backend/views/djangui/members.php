<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Episode;
use common\models\Season;
use common\models\Djangui;
use common\models\DjanguiMember;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use common\models\Parameter;
use yii\helpers\BaseUrl;

/* @var $this yii\web\View */
/* @var $model common\models\ServiceProvider */

$this->title = is_null($modelSeason) ? "" : $modelSeason->name.' - Members';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Seasons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="service-provider-view">

    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-6">
            <div class="card border-primary">
                <div class="card-header"> <?= Yii::t('app', 'Inputs') ?> </div>
                <div class="card-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <?= 
                       $form->field($modelDjanguiMember, 'season')->widget(Select2::classname(), [
                            'data' => Season::getAllSeasons(),
                            'options' => ['placeholder' => Yii::t('app', 'Select a Season').' ...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ])
                    ?>

                    <?= 
                       $form->field($modelDjanguiMember, 'djangui')->widget(Select2::classname(), [
                            'data' => Djangui::getAllDjanguis(),
                            'options' => ['placeholder' => Yii::t('app', 'Select a Djangui').' ...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ])
                    ?>

                    <?= 
                       $form->field($modelDjanguiMember, 'collecting_episode')->widget(Select2::classname(), [
                            'data' => Episode::getAllEpisodes(is_null($modelSeason) ? 0 : $modelSeason->id),
                            'options' => ['placeholder' => Yii::t('app', 'Select an Episode').' ...'],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ],
                        ])
                    ?>

                    <?= $form->field($modelDjanguiMember, 'member')->widget(Select2::classname(), [
                        'data' => User::getAllUsers(),
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select a Member').' ...',
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ])?>

                    <br/>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6 col-sm-2 col-xs-6">
                                <?= Html::submitButton($modelDjanguiMember->isNewRecord ? Yii::t('app', '+ Member') : Yii::t('app', 'Update'), ['class' => $modelDjanguiMember->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'save']) ?>
                            </div>
                            <div class="col-md-2 col-sm-2 col-xs-2"> </div>
                            <div class="col-md-4 col-sm-4 col-xs-4">
                                <?= Html::submitButton(Yii::t('app', 'Load'), ['class' => 'btn btn-info', 'name' => 'load']) ?>
                            </div>
                        </div>
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>

        <div class="col-md-8 col-sm-6 col-xs-6">
            <div class="card border-primary">
                <div class="card-header"> <?= Yii::t('app', 'Members Details') ?> </div>
                <div class="card-body">
                    <?php Pjax::begin(); ?>
                    <?= GridView::widget([
                        'dataProvider' => $membersDataProvider,
                        //'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            //'id',                            
                            [
                                'attribute' => 'collecting_episode',
                                'value'=>function ($model, $key, $index, $widget) { 
                                    return Episode::findOne($model->collecting_episode)->name;
                                },
                            ],

                            [
                                'attribute' => 'member',
                                'value'=>function ($model, $key, $index, $widget) { 
                                    return User::findOne($model->member)->name;
                                },
                            ],

                            ['class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {delete}',
                                /*'buttons' => [
                                    'update' => function ($url, $model, $key) {
                                        return '<a href="'.BaseUrl::to(['members', 'id' => $model->season, 'action' => 'UPDATE', 'djanguiMemberId' => $model->id]).'" title="'.\Yii::t('app', "Update").'" data-pjax="0"><span class="glyphicon glyphicon-pencil"></span></a>';
                                    },

                                    'delete' => function ($url, $model, $key) {
                                        return '<a href="'.BaseUrl::to(['members', 'id' => $model->season, 'action' => 'DELETE', 'djanguiMemberId' => $model->id]).'" title="'.\Yii::t('app', "Delete").'" data-pjax="0"><span class="glyphicon glyphicon-trash"></span></a>';
                                    },
                                ],  */
                                'urlCreator' => function ($action, DjanguiMember $model, $key, $index, $column) {
                                    return Url::toRoute(['members', 'seasonId' => $model->season, 'djanguiId' => $model->djangui ,'currentAction' =>strtoupper($action) , 'djanguiMemberId' => $model->id]);
                                }              
                            ],
                        ],
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>  
            </div>
        </div>
    </div>
</div>