<?php
/**
 * @link http://www.infinitecascade.com/
 * @copyright Copyright (c) 2014 Infinite Cascade
 * @license http://www.infinitecascade.com/license/
 */

namespace cascade\components\web\browser;

use Yii;

/**
 * Response [@doctodo write class description for Response]
 *
 * @author Jacob Morrison <email@ofjacob.com>
**/
class Response extends \infinite\web\browser\Response
{
    /**
     * @inheritdoc
     */
    public $bundleClass = 'cascade\\components\\web\\browser\\Bundle';

    /**
     * __method_defaultInstructions_description__
     * @return __return_defaultInstructions_type__ __return_defaultInstructions_description__
     */
    public static function defaultInstructions()
    {
        return [
            'offset' => 0
        ];
    }
    /**
     * __method_parseStack_description__
     * @param __param_request_type__     $request __param_request_description__
     * @return __return_parseStack_type__ __return_parseStack_description__
     */
    public static function parseStack($request)
    {
        $instructions = [];
        if (empty($request['stack'])) {
            return false;
        }

        $lastItem = array_pop($request['stack']);
        if (!isset($lastItem['type'])) { return false; }
        $instructions['id'] = $request['id'];
        $registryClass = Yii::$app->classes['Registry'];
        switch ($lastItem['type']) {
            case 'type': //object type
                $parentItem = false;
                $instructions['handler'] = 'objects';
                if (!empty($request['stack'])) {
                    $parentItem = array_pop($request['stack']);
                }
                $type = Yii::$app->collectors['types']->getOne($lastItem['id']);
                if (!$type) { return false; }
                $instructions['type'] = $lastItem['id'];
                if ($parentItem && $parentItem['type'] === 'object' && !empty($parentItem['id'])) {
                    $instructions['parent'] = $parentItem['id'];
                }
            break;
            case 'object': //object type
                $object = $registryClass::getObject($lastItem['id']);
                if (!$object) { return false; }
                $objectTypeItem = $object->objectTypeItem;
                $objectType = $objectTypeItem->object;
                if (!isset($request['modules'])) {
                    $request['modules'] = array_keys(Yii::$app->collectors['types']->getAll());
                }
                $possibleTypes = HandlerTypes::possibleTypes($objectType, $request['modules']);

                //\d(array_keys($possibleTypes));exit;
                if (empty($possibleTypes)) {
                    return false;
                } elseif (count($possibleTypes) === 1) {
                    $type = array_pop($possibleTypes);
                    $instructions['handler'] = 'objects';
                    $instructions['type'] = $type->systemId;
                    $instructions['parent'] = $object->primaryKey;
                } else {
                    $instructions['handler'] = 'types';
                    $instructions['limitTypes'] = array_keys($possibleTypes);
                }
            break;
        }

        return $instructions;
    }
}