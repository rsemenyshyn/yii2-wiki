<?php
namespace d4yii2\yii2\wiki\accessRights;


use CompanyRights\components\UserRoleInterface;
use yii2d3\d3persons\accessRights\SystemAdminUserRole;
use Yii;

class WikiEditUserRole implements UserRoleInterface
{

    const NAME = 'WikiEdit';
    /**
     * @inheritdoc
     */
    public function getType()
    {
        return self::TYPE_REGULAR;
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return Yii::t('app', 'Wiki Edit');
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * @inheritdoc
     */
    public function getAssigments()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function canAssign()
    {
        return Yii::$app->user->can(SystemAdminUserRole::NAME);
    }
    
    /**
     * @inheritdoc
     */    
    public function canView()
    {

        return Yii::$app->user->can(SystemAdminUserRole::NAME);
    }

    /**
     * @inheritdoc
     */
    public function canRevoke()
    {
        return Yii::$app->user->can(SystemAdminUserRole::NAME);
    }            
    

            
}
