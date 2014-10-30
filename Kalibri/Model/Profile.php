<?php

namespace Kalibri\Model;

use \Kalibri\Model\Entity\Profile as ProfileEntity;

class Profile extends ActiveEntity
{

//------------------------------------------------------------------------------------------------//
    /**
     * @param string $login
     *
     * @return \Kalibri\Model\Entity\Profile
     */
    public function getByLogin( $login )
    {
        return $this->getBy($login, 'login');
    }

//------------------------------------------------------------------------------------------------//
    public function register( ProfileEntity $data )
    {
        if( $data->getLogin() && $data->getPassword() )
        {
            $this->insert( $data );
            return true;
        }

        return false;
    }
}