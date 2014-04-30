<?php
/**
 * @link http://www.infinitecascade.com/
 * @copyright Copyright (c) 2014 Infinite Cascade
 * @license http://www.infinitecascade.com/license/
 */

namespace cascade\components\db\fields\formats;

/**
 * Text [@doctodo write class description for Text]
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class Text extends Base
{
    /**
    * @inheritdoc
     */
    public function get()
    {
        $result = strip_tags($this->field->value);
        $result = preg_replace("/\n+/", "\n", $result);
        if (empty($result)) {
            $result = '<span class="empty">(none)</span>';
        }

        return $result;
    }
}
