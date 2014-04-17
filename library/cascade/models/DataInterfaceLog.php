<?php
/**
 * @link http://www.infinitecascade.com/
 * @copyright Copyright (c) 2014 Infinite Cascade
 * @license http://www.infinitecascade.com/license/
 */

namespace cascade\models;

/**
 * DataInterfaceLog is the model class for table "data_interface_log".
 *
 * @property string $id
 * @property string $data_interface_id
 * @property string $status
 * @property string $message
 * @property integer $peak_memory
 * @property string $started
 * @property string $ended
 *
 * @property DataInterface $dataInterface
 *
 * @author Jacob Morrison <email@ofjacob.com>
 */
class DataInterfaceLog extends \cascade\components\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function isAccessControlled()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'data_interface_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['data_interface_id'], 'required'],
            [['message'], 'string'],
            [['peak_memory'], 'integer'],
            [['started', 'ended'], 'safe'],
            [['data_interface_id'], 'string', 'max' => 36],
            [['status'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'data_interface_id' => 'Data Interface ID',
            'status' => 'Status',
            'message' => 'Message',
            'peak_memory' => 'Peak Memory',
            'started' => 'Started',
            'ended' => 'Ended',
        ];
    }

    /**
     * @return \yii\db\ActiveRelation
     */
    public function getDataInterface()
    {
        return $this->hasOne(DataInterface::className(), ['id' => 'data_interface_id']);
    }
}
