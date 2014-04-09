<?php
namespace cascade\components\web\widgets\base;

use Yii;

use infinite\helpers\Html;
use infinite\helpers\ArrayHelper;
use yii\bootstrap\ButtonDropdown;

trait ListWidgetTrait
{
	public $renderPager = true;
	public $emptyMessage = 'No items exist.';
	public $defaultContentRow = [
		'class' => 'list-group-item-text',
		'tag' => 'div'
	];
	protected $_renderContentTemplate;

	public function contentTemplate($model)
	{
		if ($model->objectType->hasDashboard && $model->can('read')) {
			return [
				'viewLink' => ['class' => 'list-group-item-heading', 'tag' => 'h5']
			];
		} else {
			return [
				'descriptor' => ['class' => 'list-group-item-heading', 'tag' => 'h5']
			];
		}
	}

	public function renderItemContent($model, $key, $index){
		$parts = [];
		foreach ($this->contentTemplate($model) as $fieldName => $settings) {
			if (is_numeric($fieldName)) {
				$fieldName = $settings;
				$settings = [];
			}
			$settings = array_merge($this->defaultContentRow, $settings);
			$tag = $settings['tag'];
			unset($settings['tag']);
			if (!empty($model->{$fieldName})) {
				$parts[] = Html::tag($tag, $model->{$fieldName}, $settings);
			}
		}

		return implode("", $parts);
	}

	public function getListOptions()
	{
		return ['class' => 'list-group'];
	}

	public function getListItemOptions($model, $key, $index)
	{
		return ['class' => 'list-group-item expandable'];
	}

	public function renderItem($model, $key, $index)
	{
		$listItemOptions = $this->getListItemOptions($model, $key, $index);
		$listTag = ArrayHelper::remove($listItemOptions, 'tag', 'li');

		$parts = [];
		$parts[] = $this->renderItemMenu($model, $key, $index);
		$parts[] = $this->renderItemContent($model, $key, $index);
		return Html::tag($listTag, implode('', $parts), $listItemOptions);
	}


	public function renderItemMenu($model, $key, $index)
	{
		$menuItems = $this->getMenuItems($model, $key, $index);
		if (!empty($menuItems)) {
			foreach ($menuItems as &$menuItem) {
				if (isset($menuItem['icon'])) {
					if (!isset($menuItem['label'])) { $menuItem['label'] = ''; }
					$menuItem['label'] = '<span class="'.$menuItem['icon'].'"></span>'. $menuItem['label'];
					unset($menuItem['icon']);
				}
			}
			return ButtonDropdown::widget([
					'label' => '',
					'options' => ['class' => 'pull-right'],
					'encodeLabel' => false,
					'dropdown' => [
						'options' => ['class' => 'pull-right'],
						'encodeLabels' => false,
						'items' => $menuItems
					]
				]);
		}
		return null;
	}

	public function generateContent() {
		$results = $this->dataProvider;
		if (!empty($results->count)) {
			$models = $this->dataProvider->getModels();
			$keys = $this->dataProvider->getKeys();

			$listOptions = $this->listOptions;
			$listTag = ArrayHelper::remove($listOptions, 'tag', 'ul');

			$rows = [];
			$rows[] = Html::beginTag($listTag, $listOptions);
			foreach (array_values($models) as $index => $model) {
				$rows[] = $this->renderItem($model, $keys[$index], $index);
			}
			$rows[] = Html::endTag($listTag);
			return implode('', $rows);
		} else {
			return Html::tag('div', $this->emptyMessage, ['class' => 'empty-messages']);
		}
	}

	public function generateFooter()
	{
		$footer = '';
		if ($this->renderPager) {
			$pager = $this->renderPager();
			if ($pager) {
				$footer = Html::tag('div', $pager, ['class' => 'panel-footer clearfix']);
			}
		}
		return parent::generateFooter() . $footer;
	}

	/**
	 * Renders the pager.
	 * @return string the rendering result
	 */
	public function renderPager()
	{
		$pagination = $this->dataProvider->getPagination();
		//\d([get_class($this), $pagination]);
		if ($pagination === false || $pagination->getPageCount() <= 1) {
			return false;
		}
		/** @var LinkPager $class */
		$pager = $this->pagerSettings;
		$class = ArrayHelper::remove($pager, 'class', 'infinite\widgets\LinkPager');
		$pager['pagination'] = $pagination;
		if (!isset($pager['options'])) {
			$pager['options'] = [];
		}
		$pager['maxButtonCount'] = 6;
		Html::addCssClass($pager['options'], 'pagination pull-right');
		return $class::widget($pager);
	}


}