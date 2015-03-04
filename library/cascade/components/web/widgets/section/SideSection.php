<?php
/**
 * @link http://www.infinitecascade.com/
 * @copyright Copyright (c) 2014 Infinite Cascade
 * @license http://www.infinitecascade.com/license/
 */

namespace cascade\components\web\widgets\section;
use cascade\components\web\widgets\decorator\EmbeddedDecorator;
use cascade\components\web\widgets\Item as WidgetItem;
use cascade\components\web\widgets\base\Header as WidgetHeader;

/**
 * SideSection [@doctodo write class description for SideSection]
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class SideSection extends Section
{
    /**
    * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->title = false;
        $this->icon = false;
    }

    /**
    * @inheritdoc
     */
    public function getWidgetDecoratorClass()
    {
        return EmbeddedDecorator::className();
    }

    /**
    * @inheritdoc
     */
    public function widgetCellSettings()
    {
        return [
            'mediumDesktopColumns' => 12,
            'tabletColumns' => 12,
            'baseSize' => 'tablet'
        ];
    }

    /**
     * __method_isSingle_description__
     * @return __return_isSingle_type__ __return_isSingle_description__
     */
    public function isSingle()
    {
        return false;
    }

    /**
    * @inheritdoc
     */
    public function defaultItems($parent = null)
    {
        $default = [];
        $default['_header'] = [
            'object' => [
                'class' => WidgetItem::className(),
                'widget' => ['class' => WidgetHeader::className()],
            ]
        ];
        return $default;
    }
}
