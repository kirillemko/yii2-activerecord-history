<?php

namespace kirillemko\activeRecordHistory\models;


use App\domain\ACL\models\AclGroupsPermissions;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * @property $id
 * @property $user_id
 * @property $event
 * @property $model
 * @property $model_id
 * @property $field_name
 * @property $old_value
 * @property $new_value
 * @property $created_at
 */
class ActiveRecordHistory extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%activerecord_models_history}}';
    }


    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'updatedAtAttribute' => false,
                'value' => null,
            ],
            [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => false,
            ]
        ];
    }

    public function fields()
    {
        return array_merge(parent::fields(),[
            'user' => function() {
                return $this->user;
            },
            'field_full_name' => function() {
                return $this->field_name
                    ? $this->model::instance()->getAttributeLabel($this->field_name)
                    : null;
            },
            'type_full_name' => function() {
                $name = $this->event;
                try{
                    $name = \Yii::t('ARHistory', $name);
                } catch (\Exception $e){ }

                return $name;
            },
            'model_full_name' => function() {
                $name = $this->model;
                try{
                    $name = \Yii::t('ARHistory', 'model.' . $name);
                } catch (\Exception $e){ }

                return $name;
            }
        ]);
    }




    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\Yii::$app->user->identityClass, ['id' => 'user_id']);
    }

}
