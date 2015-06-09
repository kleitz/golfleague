<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Url;
use common\behaviors\Constant;

/**
 * This is the model class for table "registrations".
 */
class Registration extends _Registration
{
	use Constant;

    /** Golfer has not registered to the competition, registration does not exist */
    const STATUS_UNREGISTERED = 'UNREGISTERED';

    /** Golfer has just registered to the competition */
    const STATUS_PENDING = 'PENDING';

    /** Registered: Golfer is registered to the competition */
    const STATUS_REGISTERED = 'REGISTERED';

    /** Golfer registration is rejected */
    const STATUS_REJECTED = 'REJECTED';

    /** Starter has confirmed registration for next event */
    const STATUS_CONFIRMED = 'CONFIRMED';

    /** Golfer cancelled registration before event */
    const STATUS_CANCELLED = 'CANCELLED';

    /** Golfer is confirmed but does not participage to competition */
    const STATUS_FORFEIT = 'FORFEIT';

    /** Golfer played but is eliminated by scoring position */
    const STATUS_MISSEDCUT = 'MISSEDCUT';

    /** Golfer played but is eliminated by competitor */
    const STATUS_ELIMINATED = 'ELIMINATED';

    /** Golfer is disqualified form compeition, reason in comments */
    const STATUS_DISQUALIFIED = 'DISQUALIFIED';

    /** Golfer has legitimately widthdrawn form competition */
    const STATUS_WITHDRAWN = 'WITHDRAWN';

    /** Golfer is invited to next stage */
    const STATUS_QUALIFIED = 'QUALIFIED';

	/** Score types */
	const SCORE_GROSS = 'GROSS';
	const SCORE_NET = 'NET';
	const SCORE_POINTS = 'POINTS';
	const SCORE_POSITION = 'POSITION';
	
    /** Special action keyword */
    const ACTION_DELETE = 'DELETE';


    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
                'timestamp' => [
                        'class' => 'yii\behaviors\TimestampBehavior',
                        'attributes' => [
                                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                                ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                        ],
                        'value' => function() { return date('Y-m-d H:i:s'); /* mysql datetime format is ‘AAAA-MM-JJ HH:MM:SS’*/},
                ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(
			parent::rules(),
			[
            [['thru'], 'in', 'range' => Hole::validNumber()],
            [['status'], 'in', 'range' => array_keys(self::getConstants('STATUS_'))],
        	]
		);
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('igolf', 'Registration'),
            'competition_id' => Yii::t('igolf', 'Competition'),
            'golfer_id' => Yii::t('igolf', 'Golfer'),
            'flight_id' => Yii::t('igolf', 'Flight'),
            'team_id' => Yii::t('igolf', 'Team'),
            'tees_id' => Yii::t('igolf', 'Tees'),
            'points' => Yii::t('igolf', 'Points'),
            'position' => Yii::t('igolf', 'Position'),
            'note' => Yii::t('igolf', 'Note'),
            'score' => Yii::t('igolf', 'Score'),
            'score_net' => Yii::t('igolf', 'Score Net'),
            'status' => Yii::t('igolf', 'Status'),
            'created_at' => Yii::t('igolf', 'Created At'),
            'updated_at' => Yii::t('igolf', 'Updated At'),
        ];
    }



	static public function getLocalizedPreCompetitionStatuses() {
		$s = [];
		foreach([
		    self::STATUS_UNREGISTERED,
		    self::STATUS_PENDING,
		    self::STATUS_REGISTERED,
		    self::STATUS_REJECTED,
		    self::STATUS_CONFIRMED,
		    self::STATUS_CANCELLED,
		] as $r)
			$s[$r] = Yii::t('igolf', $r);
		return $s;
	}
	
	static public function getPreCompetitionStatuses() {
		return array_keys(self::getLocalizedPreCompetitionStatuses());
	}


	static public function getLocalizedPostCompetitionStatuses() {
		$s = [];
		foreach([
		    self::STATUS_FORFEIT,
		    self::STATUS_MISSEDCUT,
		    self::STATUS_ELIMINATED,
		    self::STATUS_DISQUALIFIED,
		    self::STATUS_WITHDRAWN,
		    self::STATUS_QUALIFIED,
		] as $r)
			$s[$r] = Yii::t('igolf', $r);
		return $s;
	}

	static public function getPostCompetitionStatuses() {
		return array_keys(self::getLocalizedPostCompetitionStatuses());
	}

	

	/**
	 * Also delete parent registration if not used
	 */
	public function hasChildren() {
		$has_children = false;
		if( $competition = $this->competition ) {
			foreach($competition->getCompetitions()->each() as $child) {
				if(!$has_children)
					$has_children = Registration::find()->andWhere(['competition_id' => $child->id, 'golfer_id' => $this->golfer_id])->exists();
			}
		}
		Yii::trace('return='.($has_children ? 'true' : 'false'), 'hasChildren');
		return $has_children;
	}


	/**
	 * Also delete parent registration if not used
	 */
	public function delete() {
		if(!$this->hasChildren())
			parent::delete();
	}

	/**
	 * Returns registration's associated scorecard. Creates one if none exists.
	 *
	 * @param boolean $detailed Whether to create hole detail score for scorecard
	 *
	 * return common\models\Scorecard
	 */
	public function getScorecard($detailed = false) {
		if(! $scorecard = $this->getScorecards()->one() ) { // Scorecard::findOne(['registration_id'=>$registration->id])
			$scorecard = new Scorecard([
				'registration_id' => $this->id,
				'tees_id' => $this->tees_id,
				'golfer_id' => $this->golfer_id,
			]);
			$scorecard->save();
			if($detailed) {
				$scorecard->makeScores();
			}
		}
		return $scorecard;
	}

}
