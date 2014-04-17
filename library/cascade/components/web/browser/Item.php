<?php
/**
 * @link http://www.infinitecascade.com/
 * @copyright Copyright (c) 2014 Infinite Cascade
 * @license http://www.infinitecascade.com/license/
 */

namespace cascade\components\web\browser;

/**
 * Item [@doctodo write class description for Item]
 *
 * @author Jacob Morrison <email@ofjacob.com>
**/
class Item extends \infinite\web\browser\Item
{
    public $objectType = false;

    public function package()
    {
        return parent::package() + [
            'objectType' => $this->objectType
        ];
    }
}
