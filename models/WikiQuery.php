<?php
namespace d4yii2\yii2\wiki\models;

/**
 * This is the ActiveQuery class for [[\app\models\Wiki]].
 * @see \app\models\Wiki
 *
 * @license MIT
 */
class WikiQuery extends \yii\db\ActiveQuery
{

	/**
	 * Named scope to filter articles with a link to another article
	 *
	 * @param $articleId
	 * @return $this
	 */
	public function withLinkToArticle($articleId)
	{
		$this->andFilterWhere(['like', 'content', '[%]('.$articleId.')', ['%'=>'%']]);
		return $this;
	}

}
