<?php
namespace cascade\components\rest;

use Yii;
use yii\base\InvalidParamException;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;

class IndexAction extends \yii\rest\IndexAction
{
	use ActionTrait;

	public $parentObject;
	protected $_dataProvider;

	public function getRequiredParams()
	{
		$requiredParams = parent::getRequiredParams();
		$modelClass = $this->modelClass;
    	$objectType = (new $modelClass)->objectType;
    	if (empty($objectType)) {
            throw new InvalidParamException($modelClass .' does not have a corresponding object type');
    	}
    	if (!$objectType->hasDashboard) {
    		$requiredParams[] = 'parentObject';
    	}
		return $requiredParams;
	}

	public function params()
	{
		return ['parentObject'];
	}

	protected function prepareDataProvider()
    {
    	if (!isset($this->_dataProvider)) {
    		$this->_dataProvider = $dataProvider = parent::prepareDataProvider();
	    	$modelClass = $this->modelClass;
	    	$objectType = (new $modelClass)->objectType;
	    	if (empty($objectType)) {
	            throw new InvalidParamException($modelClass .' does not have a corresponding object type');
	    	}
	    	if (!isset($this->parentObject)) {
	    		$dataProvider->query->denyInherit();
	    	} else {
	    		$registryClass = Yii::$app->classes['Registry'];
	    		$parentObject = $registryClass::get($this->parentObject, false);
	    		if (!$parentObject) {
            		throw new NotFoundHttpException("Object not found: {$this->parentObject}");
	    		}
	    		if (!$parentObject->can('read')) {
	    			throw new ForbiddenHttpException("Unable to access {$this->parentObject}");
	    		}
	    		$newQuery = $parentObject->queryChildObjects($this->modelClass);
	    		$this->_dataProvider->query = $newQuery;
	    	}
	    }
    	return $this->_dataProvider;
    }
}