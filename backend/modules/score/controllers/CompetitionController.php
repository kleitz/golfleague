<?php

namespace backend\modules\score\controllers;

use backend\controllers\DefaultController as GolfLeagueController;
use common\models\Competition;
use common\models\search\CompetitionSearch;
use common\models\Match;

use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\helpers\Url;

/**
 * CompetitionController implements the CRUD actions for Competition model.
 */
class CompetitionController extends GolfLeagueController
{
    /**
     * Lists all Competition models of interest for Starter.
     * @return mixed
     */
    public function actionIndex()
    {
		$now = date('Y-m-d H:i:s');

		/** Competitions that are ready to be played.
		 */
		$readySearch = new CompetitionSearch();
        $readyProvider = new ActiveDataProvider([
            'query' => Match::find()->where(['status' => Competition::STATUS_READY])
										  ->andWhere(['<=','start_date', $now])
        ]);

		/** Ongoing competition.
		 */
		$openSearch = new CompetitionSearch();
        $openProvider = new ActiveDataProvider([
            'query' => Match::find()->where(['status' => Competition::STATUS_READY])
										  ->andWhere(['>','start_date', $now])
        ]);

		/** Competitions that are ready to be played.
		 */
		$openTournamentSearch = new CompetitionSearch();
	    $openTournamentProvider = new ActiveDataProvider([
	        'query' => Competition::find()->where(['competition_type' =>[Competition::TYPE_TOURNAMENT, Competition::TYPE_SEASON], 'status' => Competition::STATUS_OPEN])
	    ]);

		/** Competitions that are ready to be played.
		 */
		$readyTournamentSearch = new CompetitionSearch();
	    $readyTournamentProvider = new ActiveDataProvider([
	        'query' => Competition::find()->where(['competition_type' =>[Competition::TYPE_TOURNAMENT, Competition::TYPE_SEASON], 'status' => Competition::STATUS_READY])
	    ]);

		/** Awaiting scores competition.
		 *  Registration must be OPEN and we must be before the registration 
		 */
		$completedSearch = new CompetitionSearch();
		$completedProvider = new ActiveDataProvider([
            'query' => Competition::find()->where(['status' => Competition::STATUS_COMPLETED])
        ]);

		/** Closed or terminated competition.
		 *  Registration must be CLOSED. 
		 */
		$closedSearch = new CompetitionSearch();
		$closedProvider = new ActiveDataProvider([
            'query' => Competition::find()->where(['status' => Competition::STATUS_CLOSED]),
        ]);

    	return $this->render('index', [
	        'readyProvider' => $readyProvider,
	        'readySearch'   => $readySearch,
	        'openProvider'	=> $openProvider,
	        'openSearch'	=> $openSearch,
	        'completedProvider'	=> $completedProvider,
	        'completedSearch'	=> $completedSearch,
	        'closedProvider' => $closedProvider,
	        'closedSearch'	=> $closedSearch,
	
			'openTournamentSearch' => $openTournamentSearch,
			'openTournamentProvider' => $openTournamentProvider,
			'readyTournamentSearch' => $readyTournamentSearch,
			'readyTournamentProvider' => $readyTournamentProvider,
        ]);
    }

    /**
     * Lists all Competition models of interest for Starter.
     * @return mixed
     */
    public function actionTournaments()
    {
		$now = date('Y-m-d H:i:s');

		/** Awaiting scores competition.
		 *  Registration must be OPEN and we must be before the registration 
		 */
		$completedSearch = new CompetitionSearch();
		$completedProvider = new ActiveDataProvider([
            'query' => Competition::find()->where(['competition_type' =>[Competition::TYPE_TOURNAMENT, Competition::TYPE_SEASON], 'status' => Competition::STATUS_COMPLETED])
        ]);

		/** Closed or terminated competition.
		 *  Registration must be CLOSED. 
		 */
		$closedSearch = new CompetitionSearch();
		$closedProvider = new ActiveDataProvider([
            'query' => Competition::find()->where(['competition_type' =>[Competition::TYPE_TOURNAMENT, Competition::TYPE_SEASON], 'status' => Competition::STATUS_CLOSED]),
        ]);

    	return $this->render('tournaments', [
	        'readyProvider' => $readyProvider,
	        'readySearch'   => $readySearch,
	        'openProvider'	=> $openProvider,
	        'openSearch'	=> $openSearch,
	        'completedProvider'	=> $completedProvider,
	        'completedSearch'	=> $completedSearch,
	        'closedProvider' => $closedProvider,
	        'closedSearch'	=> $closedSearch,
        ]);
    }

    /**
     * Displays a single Competition model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single Competition model.
     * @param integer $id
     * @return mixed
     */
    public function actionResult($id)
    {
        return $this->render('result', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Displays a single Competition model.
     * @param integer $id
     * @return mixed
     */
    public function actionLeaderboard($id)
    {
        return $this->render('leaderboard', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing Competition model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Finds the Competition model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Competition the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Competition::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     * Displays a single Competition model.
     * @param integer $id
     * @return mixed
     */
    public function actionRule($id)
    {
        return $this->render('rule', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Apply rule to Tournament or Season to compile results from children competitions.
     */
    public function actionApply($id)
    {
		$competition = $this->findModel($id);

		if($competition->status == Competition::STATUS_READY) {
			if($competition->rule) {
				$competition->rule->apply($competition);
				Yii::$app->session->setFlash('success', Yii::t('igolf', 'Rule «{0}» applied.', $competition->rule->name));
			} else {
				Yii::$app->session->setFlash('info', Yii::t('igolf', 'No final rule to apply.'));
			}
		} else {
			Yii::$app->session->setFlash('danger', Yii::t('igolf', 'Completition is not completed.'));
		}

        return $this->redirect(Url::to(['scorecard/competition', 'id' => $id]));
    }

    /**
     * Apply final rule to competition.
     */
    public function actionApplyFinal($id)
    {
		$competition = $this->findModel($id);

		if($competition->finalRule) {
			$competition->finalRule->apply($competition);
			Yii::$app->session->setFlash('success', Yii::t('igolf', 'Rule applied.'));
		} else {
			Yii::$app->session->setFlash('info', Yii::t('igolf', 'No final rule to apply.'));
		}

        return $this->redirect(Url::to(['rule', 'id' => $id]));
    }

    /**
     * Publish a competition results.
     */
    public function actionPublish($id)
    {
		$competition = $this->findModel($id);

		$competition->status = Competition::STATUS_CLOSED;
		$competition->save();
		
		if($parent = $competition->parent) {
			$parent->prepareScorecards();
			$parent->status = Competition::STATUS_READY;
			$parent->save();
		}

        return $this->redirect(Url::to(['result', 'id' => $id]));
    }

}
