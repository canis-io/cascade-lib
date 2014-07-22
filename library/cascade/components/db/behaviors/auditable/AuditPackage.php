<?php
/**
 * @link http://www.infinitecascade.com/
 * @copyright Copyright (c) 2014 Infinite Cascade
 * @license http://www.infinitecascade.com/license/
 */

namespace cascade\components\db\behaviors\auditable;

use Yii;
use ArrayIterator;
use infinite\helpers\ArrayHelper;
use cascade\models\ObjectFamiliarity;

/**
 * ActiveRecord is the model class for table "{{%active_record}}".
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class AuditPackage extends \infinite\base\Object implements \IteratorAggregate, \ArrayAccess, \Countable
{
	protected $_items = [];

	public function __construct($dataSource)
	{
		foreach ($dataSource->models as $item) {
			$this->add($item);
		}
		parent::__construct();
	}

	public function count()
    {
        return $this->getCount();
    }

    /**
     * Returns the number of items in the collection.
     * @return integer the number of items in the collection.
     */
    public function getCount()
    {
        return count($this->_items);
    }

    /**
     * Returns the item with the specified name.
     * @param string $name the item name
     * @return object the item with the specified name. Null if the named item does not exist.
     * @see getValue()
     */
    public function get($name)
    {
        return isset($this->_items[$name]) ? $this->_items[$name] : null;
    }

    /**
     * Returns whether there is a item with the specified name.
     * Note that if a item is marked for deletion from browser, this method will return false.
     * @param string $name the item name
     * @return boolean whether the named item exists
     * @see remove()
     */
    public function has($name)
    {
        return isset($this->_items[$name]);
    }

    /**
     * Adds a item to the collection.
     * If there is already a item with the same name in the collection, it will be removed first.
     * @param object $item the item to be added
     * @throws InvalidCallException if the item collection is read only
     */
    public function add($item)
    {
        $this->_items[$item->primaryKey] = $item;
    }

    /**
     * Removes a item.
     * If `$removeFromBrowser` is true, the item will be removed from the browser.
     * In this case, a item with outdated expiry will be added to the collection.
     * @param string $item the item object or the name of the item to be removed.
     * @throws InvalidCallException if the item collection is read only
     */
    public function remove($item)
    {
    	if (is_object($item)) {
        	unset($this->_items[$item->primaryKey]);
    	} else {
    		unset($this->_items[$item]);
    	}
    }

    /**
     * Removes all items.
     * @throws InvalidCallException if the item collection is read only
     */
    public function removeAll()
    {
        $this->_items = [];
    }

    /**
     * Returns the collection as a PHP array.
     * @return array the array representation of the collection.
     * The array keys are item names, and the array values are the corresponding item objects.
     */
    public function toArray()
    {
    	$p = [];
    	$p['timestamp'] = time();
    	$p['activity'] = [];
    	$p['objects'] = [];
    	foreach ($this as $item) {
    		$eventObject = $item->eventObject;
    		$package = $eventObject->package;
    		$p['activity'][$item->primaryKey] = [];
    		$p['activity'][$item->primaryKey]['primaryObject'] = $package['primaryObject'];
    		$p['activity'][$item->primaryKey]['story'] = $package['story'];
    		$p['activity'][$item->primaryKey]['timestamp'] = $package['timestamp'];

    		foreach ($package['objects'] as $object) {
    			if (isset($p['objects'][$object->primaryKey])) {
    				continue;
    			}
    			$p['objects'][$object->primaryKey] = $object->package;
    			unset($p['objects'][$object->primaryKey]['id']);
    		}
    	}
        return $p;
    }

    /**
     * Returns whether there is a item with the specified name.
     * This method is required by the SPL interface `ArrayAccess`.
     * It is implicitly called when you use something like `isset($collection[$name])`.
     * @param string $name the item name
     * @return boolean whether the named item exists
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * Returns the item with the specified name.
     * This method is required by the SPL interface `ArrayAccess`.
     * It is implicitly called when you use something like `$item = $collection[$name];`.
     * This is equivalent to [[get()]].
     * @param string $name the item name
     * @return Object the item with the specified name, null if the named item does not exist.
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * Adds the item to the collection.
     * This method is required by the SPL interface `ArrayAccess`.
     * It is implicitly called when you use something like `$collection[$name] = $item;`.
     * This is equivalent to [[add()]].
     * @param string $name the item name
     * @param Object $item the item to be added
     */
    public function offsetSet($name, $item)
    {
        $this->add($item);
    }

    /**
     * Removes the named item.
     * This method is required by the SPL interface `ArrayAccess`.
     * It is implicitly called when you use something like `unset($collection[$name])`.
     * This is equivalent to [[remove()]].
     * @param string $name the item name
     */
    public function offsetUnset($name)
    {
        $this->remove($name);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->_items);
    }

}
