<?php
use infinite\helpers\Html;
use cascade\models\SearchForm;
use yii\widgets\ActiveForm;

cascade\components\web\assetBundles\QueryBuilderAsset::register($this);
cascade\components\web\assetBundles\BrowseFilterAsset::register($this);

$js = [];
$searchModel = new SearchForm;
$searchForm = ActiveForm::begin([
    'id' => 'filter-form',
    'enableClientValidation' => false,
]);
echo Html::beginTag('ul', ['class' => 'nav nav-tabs', 'role' => 'tablist']);
$simpleOptions = [];
Html::addCssClass($simpleOptions, 'active');
echo Html::beginTag('li', $simpleOptions);
echo Html::a('Simple', '#simple-filter', ['role' => 'tab', 'data-toggle' => 'tab']);
echo Html::endTag('li');

$advancedOptions = [];
echo Html::beginTag('li', $advancedOptions);
echo Html::a('Advanced', '#advanced-filter', ['role' => 'tab', 'data-toggle' => 'tab']);
echo Html::endTag('li');

echo Html::endTag('ul');

echo Html::beginTag('div', ['class' => 'tab-content well']);
// simple
Html::addCssClass($simpleOptions, 'tab-pane');
$simpleOptions['id'] = 'simple-filter';
echo Html::beginTag('div', $simpleOptions);

echo $searchForm->field($searchModel, 'query',
    [
        'inputOptions' => ['id' => 'simple-filter-input', 'placeholder' => 'Search', 'class' => 'form-control'],
        //'template' => '{input}',
    ]);
echo Html::endTag('div');

// advanced
Html::addCssClass($advancedOptions, 'tab-pane');
$advancedOptions['id'] = 'advanced-filter';
echo Html::beginTag('div', $advancedOptions);
$queryBuilderOptions = [];
$queryBuilderOptions['filters'] = $type->dummyModel->filterFields;
$builderId = 'advanced-filter-builder';
echo Html::tag('div', '', ['id' => $builderId]);
$js[] = "$('#{$builderId}').queryBuilder(". json_encode($queryBuilderOptions) .");";
echo Html::endTag('div');

echo Html::submitButton('Search', ['class' => 'btn btn-default']);

echo Html::endTag('div');
ActiveForm::end();

echo Html::beginTag('div', ['class' => 'panel panel-default']);
echo Html::tag('div', Html::tag('div', 'Results', ['class' => 'panel-title']), ['class' => 'panel-heading']);

echo Html::beginTag('div', ['class' => 'panel-body', 'id' => 'filter-results']);
echo 'YEAH';
echo Html::endTag('div');
echo Html::endTag('div');


$this->registerJs(implode("\n", $js));

?>