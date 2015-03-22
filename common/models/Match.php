<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "competitions" of type Match.
 *
 * @property Tournament $tournament
 */
class Match extends Competition
{
	const COMPETITION_TYPE = self::TYPE_MATCH;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
			parent::rules(),
			[
	            [['course_id', 'holes', 'rule_id', 'start_date'], 'required'],
        	]
		);
    }



    public static function defaultScope($query)
    {
        $query->andWhere(['competition_type' => self::TYPE_MATCH]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTournament()
    {
        return $this->hasOne(Tournament::className(), ['id' => 'parent_id']);
    }
}