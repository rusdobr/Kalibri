<?php

namespace Kalibri;

/**
 *  @package Kalibri
 *
 *  @author <a href="mailto:kostinenko@gmail.com">Alexander Kostynenko</a>
 */
class Auth
{
    /**
     * Active user ID
     * @var int
     */
    protected $myProfileId;

    /**
     * Local cache for profiles
     * @var array
     */
    protected $profiles = array();

    /**
     * @var \Kalibri\Model\Profile
     */
    protected $model;

//------------------------------------------------------------------------------------------------//
    public function __construct()
    {
        $this->model = \Kalibri::model('profile');
        $this->myProfileId = isset($_SESSION['user-id'])? $_SESSION['user-id']: null;
    }

//------------------------------------------------------------------------------------------------//
    public function tryLogin( $login, $rawPassword )
    {
        if( $this->isValidLogin( $login ) )
        {
            /**@var $profile \Kalibri\Model\Entity\Profile**/
            $profile = $this->model->getByLogin( $login );
            $password = $this->encryptPassword( $rawPassword );

            if( $profile && $profile->getPassword() === $password )
            {
                $this->myProfileId = $_SESSION['user-id'] = $profile->getProfileId();
                $this->profiles[ $this->myProfileId ] = $profile;

                return true;
            }
        }

        return false;
    }

//------------------------------------------------------------------------------------------------//
    public function authorizeById( $userId )
    {
        $this->logout();

        $profile = $this->model->get( $userId );

        if( $profile )
        {
            $this->myProfileId = $_SESSION['user-id'] = $profile->getProfileId();
            return true;
        }

        return false;
    }

//------------------------------------------------------------------------------------------------//
    public function logout()
    {
        session_destroy();
        session_start();
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @param int $profileId
     *
     * @return \Kalibri\Model\Entity\Profile
     */
    public function getProfile( $profileId = null )
    {
        $profileId = $profileId === null? $this->myProfileId: (int)$profileId;

        if( !$profileId )
        {
            return null;
        }

        return $this->model->get( $profileId );
    }

//------------------------------------------------------------------------------------------------//
    /**
     * @deprecated
     * @param string $login
     *
     * @return \Kalibri\Model\Entity\Profile
     */
    public function getProfileByLogin( $login )
    {
        return $this->model->getByLogin($login);
    }

//------------------------------------------------------------------------------------------------//
    public function encryptPassword( $rawPassword )
    {
        return md5( $rawPassword. \Kalibri::config()->get('auth.salt') );
    }

//------------------------------------------------------------------------------------------------//
    public function isValidLogin( $login )
    {
        return (bool)preg_match( '/^[\w\d\@_\.]{4,255}$/', $login );
    }

//------------------------------------------------------------------------------------------------//
    public function getModel()
    {
        return $this->model;
    }
}