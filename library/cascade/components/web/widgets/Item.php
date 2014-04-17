<?php
/**
 * @link http://www.infinitecascade.com/
 * @copyright Copyright (c) 2014 Infinite Cascade
 * @license http://www.infinitecascade.com/license/
 */

namespace cascade\components\web\widgets;

use Yii;

use infinite\base\collector\CollectedObjectTrait;

/**
 * Item [@doctodo write class description for Item]
 *
 * @author Jacob Morrison <email@ofjacob.com>
**/
class Item extends \infinite\base\collector\Item implements \infinite\base\collector\CollectedObjectInterface
{
    use CollectedObjectTrait;

    public $name;
    public $widget;
    public $tab;
    public $priority = 0;
    public $locations = [];
    protected $_section;
    public $settings = [];

    public function getObject()
    {
        if (is_null($this->widget)) {
            return null;
        }
        $object = Yii::createObject($this->widget);
        $object->settings = $this->settings;
        $object->collectorItem = $this;

        return $object;
    }

    public function getSection($parent = null, $settings = [])
    {
        $settings = array_merge($this->settings, $settings);
        if (is_null($this->_section)) {
            $this->_section = $this->owner->getSection($parent, $settings);
        }
        if (is_callable($this->_section) || (is_array($this->_section) && !empty($this->_section[0]) && is_object($this->_section[0]))) {
            return $this->evaluateExpression($this->_section, ['parent' => $parent, 'settings' => $settings]);
        }

        return $this->_section;
    }

    public function setSection($value)
    {
        $this->_section = $value;
    }

}
