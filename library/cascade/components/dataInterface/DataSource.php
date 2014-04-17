<?php
/**
 * @link http://www.infinitecascade.com/
 * @copyright Copyright (c) 2014 Infinite Cascade
 * @license http://www.infinitecascade.com/license/
 */

namespace cascade\components\dataInterface;

use Yii;
use cascade\components\db\ActiveRecord;

/**
 * DataSource [@doctodo write class description for DataSource]
 *
 * @author Jacob Morrison <email@ofjacob.com>
**/
abstract class DataSource extends \infinite\base\Component
{
    const EVENT_LOAD_FOREIGN_DATA_ITEMS = 0x01;
    const EVENT_LOAD_LOCAL_DATA_ITEMS = 0x02;

    public $fieldMapClass = 'cascade\\components\\dataInterface\\FieldMap';
    public $dataItemClass = 'cascade\\components\\dataInterface\\DataItem';
    public $searchClass = 'cascade\\components\\dataInterface\\Search';

    public $keyGenerator;
    public $debug = false;
    public $lazyForeign = true;
    public $lazyLocal = true;
    public $childOnly = false;
    public $postProcess = false;

    public $ignoreForeign = false;
    public $ignoreLocal = false;

    public $baseAttributes = [];

    protected $_localModel;
    protected $_foreignModel;
    protected $_map;
    protected $_settings;
    protected $_search;

    protected $_foreignDataItems;
    protected $_localDataItems;

    public $name;
    public $module;

    protected $_countTotal;
    protected $_countRemaining;

    static $defaultSettings = [
        'direction' => 'to_local', // to_local, to_foreign, both
        'update' => true,
        'create' => true,
        'deleteLocal' => false,
        'deleteForeign' => false,
        'foreignPullParams' => [],
        'universalKey' => false
    ];

    // abstract public function handleLocal($action, DataItem $dataItem, DataItem $parent = null);

    public function isReady()
    {
        return isset($this->localModel) && isset($this->foreignModel);
    }

    public function clearCaches()
    {
        ActiveRecord::clearCache();
    }

    public function getTotal()
    {
        if (!$this->isReady()) { return 0; }
        if (is_null($this->_countTotal)) {
            $this->_countTotal = 0;
            if (in_array($this->settings['direction'], ['to_local', 'both'])) {
                $this->_countTotal += count($this->foreignDataItems);
            }

            if (in_array($this->settings['direction'], ['to_foreign', 'both'])) {
                $this->_countTotal += count($this->localDataItems);
            }
        }

        return $this->_countTotal;
    }

    public function getDone()
    {
        return $this->total - $this->remaining;
    }

    public function getDummyLocalModel()
    {
        $localModelClass = $this->localModel;

        return new $localModelClass;
    }

    public function getRemaining()
    {
        if (is_null($this->_countRemaining)) {
            $this->_countRemaining = $this->total;
        }

        return $this->_countRemaining;
    }

    public function reduceRemaining(DataItem $dataItem)
    {
        $n = 0;
        // if foreign (handle does foreign -> local)
        if ($dataItem->isForeign && in_array($this->settings['direction'], ['to_local', 'both'])) {
            $n++;
        }

        // if local (handle does local -> foreign)
        if (!$dataItem->isForeign && in_array($this->settings['direction'], ['to_foreign', 'both'])) {
            $n++;
        }

        $this->_countRemaining = $this->remaining - $n;
        $this->action->reduceRemaining($n);
    }

    public function getForeignDataItems()
    {
        if (!isset($this->_foreignDataItems)) {
            $this->trigger(self::EVENT_LOAD_FOREIGN_DATA_ITEMS);
        }

        return $this->_foreignDataItems;
    }

    public function getLocalDataItems()
    {
        if (!isset($this->_localDataItems)) {
            $this->trigger(self::EVENT_LOAD_LOCAL_DATA_ITEMS);
        }

        return $this->_localDataItems;
    }

    public function getHandledLocalDataItems()
    {
        $handled = [];
        foreach ($this->localDataItems as $local) {
            if ($local->handled) {
                $handled[] = $local;
            }
        }

        return $handled;
    }

    public function run()
    {
        if (!$this->isReady()) { return false; }
        $action = $this->action;
        $this->settings = $action->settings;

        if (in_array($this->settings['direction'], ['to_local', 'both'])) {
            // start foreign
            foreach ($this->foreignDataItems as $dataItem) {
                $dataItem->handler->handle();
                $this->clearCaches();
            }
        }

        if (in_array($this->settings['direction'], ['to_foreign', 'both'])) {
            // start local
            foreach ($this->localDataItems as $dataItem) {
                $dataItem->handler->handle();
                $this->clearCaches();
            }
        }

        return true;
    }

    public function getDataInterface()
    {
        return $this->_dataInterface;
    }

    public function setSettings($settings)
    {
        if (is_null($this->_settings)) {
            $this->_settings = self::$defaultSettings;
        }
        if (!is_array($settings)) { return true; }
        $this->_settings = array_merge($this->_settings, $settings);

        return true;
    }

    public function getSettings()
    {
        if (is_null($this->_settings)) {
            $this->settings = [];
        }

        return $this->_settings;
    }

    public function getSearch()
    {
        return $this->_search;
    }

    public function setSearch($value)
    {
        if (!is_object($value)) {
            if (!isset($value['class'])) {
                $value['class'] = $this->searchClass;
            }
            $value = Yii::createObject($value);
        }
        $value->dataSource = $this;
        $this->_search = $value;
    }

    public function getLocalModel()
    {
        return $this->_localModel;
    }

    public function setLocalModel($value)
    {
        $this->_localModel = ActiveRecord::parseModelAlias($value);
    }

    public function setForeignModel($value)
    {
        $this->_foreignModel = $value;
    }

    public function getForeignModel()
    {
        return $this->_foreignModel;
    }

    public function setMap($m)
    {
        foreach ($m as $k => $v) {
            $fieldMap = $v;
            if (!isset($fieldMap['class'])) {
                $fieldMap['class'] = $this->fieldMapClass;
            }
            $fieldMap['dataSource'] = $this;
            $fieldMap = Yii::createObject($fieldMap);
            $this->_map[] = $fieldMap;
        }

        return true;
    }

    public function getMap()
    {
        return $this->_map;
    }

    public function getAction()
    {
        return $this->module->action;
    }
}
